<?php

/**
 * ownCloud - Activities App
 *
 * @author Frank Karlitschek, Joas Schilling
 * @copyright 2013 Frank Karlitschek frank@owncloud.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Audit_log;

/**
 * @brief The class to handle the filesystem hooks
 */
class Hooks {
	/**
	 * @brief Registers the filesystem hooks for basic filesystem operations.
	 * All other events has to be triggered by the apps.
	 */
	public static function register() {
		\OCP\Util::connectHook('OC_Filesystem', 'post_create', 'OCA\Audit_log\Hooks', 'fileCreate');
		\OCP\Util::connectHook('OC_Filesystem', 'post_update', 'OCA\Audit_log\Hooks', 'fileUpdate');
		\OCP\Util::connectHook('OC_Filesystem', 'delete', 'OCA\Audit_log\Hooks', 'fileDelete');
		\OCP\Util::connectHook('OC_Filesystem', 'read', 'OCA\Audit_log\Hooks', 'fileRead');
		\OCP\Util::connectHook('\OCA\Files_Trashbin\Trashbin', 'post_restore', 'OCA\Audit_log\Hooks', 'fileRestore');
		\OCP\Util::connectHook('OCP\Share', 'post_shared', 'OCA\Audit_log\Hooks', 'share');

		//\OCP\Util::connectHook('OC_User', 'post_deleteUser', 'OCA\Audit_log\Hooks', 'deleteUser');

		// hooking up the activity manager
		$am = \OC::$server->getActivityManager();
		$am->registerConsumer(function() {
			return new Consumer();
		});
	}

	/**
	 * @brief Store the create hook events
	 * @param array $params The hook params
	 */
	public static function fileCreate($params) {
		if (\OCP\User::getUser() !== false) {
			self::addNotificationsForFileAction($params['path'], Data::TYPE_SHARE_CREATED, 'created');
		} else {
			self::addNotificationsForFileAction($params['path'], Data::TYPE_SHARE_CREATED, '', 'created_public');
		}
	}

	/**
	 * @brief Store the update hook events
	 * @param array $params The hook params
	 */
	public static function fileUpdate($params) {
		self::addNotificationsForFileAction($params['path'], Data::TYPE_SHARE_CHANGED, 'changed');
	}

	/**
	 * @brief Store the download read events
	 * @param array $params The hook params
	 */
	public static function fileRead($params) {
		self::addNotificationsForFileAction($params['path'], Data::TYPE_SHARE_DOWNLOADED, 'download');
	}

	/**
	 * @brief Store the delete hook events
	 * @param array $params The hook params
	 */
	public static function fileDelete($params) {
		self::addNotificationsForFileAction($params['path'], Data::TYPE_SHARE_DELETED, 'deleted');
	}

	/**
	 * @brief Store the restore hook events
	 * @param array $params The hook params
	 */
	public static function fileRestore($params) {
		self::addNotificationsForFileAction($params['filePath'], Data::TYPE_SHARE_RESTORED, 'restored');
	}

	/**
	 * Creates the entries for file actions on $file_path
	 *
	 * @param string $filePath         The file that is being changed
	 * @param int    $activityType     The activity type
	 * @param string $subject          The subject for the actor
	 * @param string $subjectBy        The subject for other users (with "by $actor")
	 */
	public static function addNotificationsForFileAction($filePath, $activityType, $subject) {
		// Do not add activities for .part-files
		if (substr($filePath, -5) === '.part') {
			return;
		}

		$affectedUsers = self::getUserPathsFromPath($filePath);

		foreach ($affectedUsers as $user => $path) {
			$userSubject = $subject;
			$userParams = array($path, \OCP\User::getUser());

			self::addNotificationsForUser(
					$user, $userSubject, $userParams,
					$path, $activityType
			);
		}
	}


	/**
	 * Returns a "username => path" map for all affected users
	 *
	 * @param string $path
	 * @return array
	 */
	public static function getUserPathsFromPath($path) {
		list($file_path, $uidOwner) = self::getSourcePathAndOwner($path);
		return \OCP\Share::getUsersSharingFile($file_path, $uidOwner, true, true);
	}

	/**
	 * Return the source
	 *
	 * @param string $path
	 * @return array
	 */
	public static function getSourcePathAndOwner($path) {
		$uidOwner = \OC\Files\Filesystem::getOwner($path);

		if ($uidOwner != \OCP\User::getUser()) {
			\OC\Files\Filesystem::initMountPoints($uidOwner);
			$info = \OC\Files\Filesystem::getFileInfo($path);
			$ownerView = new \OC\Files\View('/'.$uidOwner.'/files');
			$path = $ownerView->getPath($info['fileid']);
		}

		return array($path, $uidOwner);
	}

	/**
	 * @brief Manage sharing events
	 * @param array $params The hook params
	 * 앱에서 설정된 시간에 메일을 보내도록 해야함
	 */
	public static function share($params) {
		if ($params['shareWith']) {
			$file_path = \OC\Files\Filesystem::getPath($params['fileSource']);
			$subject = ($params['shareType'] == \OCP\Share::SHARE_TYPE_USER) ? 'shared_user' : 'shared_group' ;
			self::addNotificationsForUser(
				$params['uidOwner'], $subject, array($file_path, $params['shareWith'], $params['uidOwner']),
				$file_path, Data::TYPE_SHARED
			);
		} else {
			self::shareFileOrFolder($params);
		}
	}

	/**
	 * Adds the activity and email for a user when the settings require it
	 *
	 * @param string $user
	 * @param string $subject
	 * @param array $subjectParams
	 * @param string $path
	 * @param bool $isFile If the item is a file, we link to the parent directory
	 * @param bool $streamSetting
	 * @param int $emailSetting
	 * @param string $type
	 */
	protected static function addNotificationsForUser($user, $subject, $subjectParams, $path, $type = Data::TYPE_SHARED) {

		

		// Add activity to mail queue
		$settings = new \OCA\Audit_log\Settings();
		$filters = $settings->getFilterstoArray();
		$datahelper = new \OCA\Audit_log\Datahelper();
		$clientInfo = $datahelper->parseUserAgent();
		if($type !== Data::TYPE_SHARE_DELETED){
            $clientInfo['filesize'] = $datahelper->getFileSize($path,$user);
            $clientInfo['checksum'] = $datahelper->getFileChecksum($path,$user);
        }

		Data::send('files', $subject, $subjectParams, $path, $user, $type);

		//필터 처리
		foreach($filters as $filter) {
			$flag = true;
			$daterange = explode(' ~ ', $filters['daterange']);
			$types = explode(',', $filters['types']);
			$device = explode(',', $filters['device']);
			$os = explode(',', $filters['os']);
			$ips = explode(',', $filters['userip']);

			//파일명
			$flag = $flag && (preg_match_all($filters['filename'], $path)>0);

			//파일크기
			if(!empty($filters['sizerange'])){
				$sizerange = explode(' ~ ', $filters['sizerange']);
				$flag = $flag && ($sizerange[0] < $clientInfo['filesize'] && $clientInfo['filesize'] < $sizerange[1]);
			}
			//기간
			$curStamp = (new DateTime())->getTimestamp();
			$stdStamp = DateTime::createFromFormat('Y-m-d',$daterange[0])->getTimestamp();
			$endStamp = DateTime::createFromFormat('Y-m-d',$daterange[1])->getTimestamp();

			$flag = $flag && $filters['consecutive']?true:($stdStamp < $curStamp && $curStamp < $endStamp);

			//유형, 디바이스, 운영체제
			$flag = $flag && in_array($clientInfo['types'], $types);
			$flag = $flag && in_array($clientInfo['device'], $device);
			$flag = $flag && in_array($clientInfo['os'], $os);

			//아이피체크
			$flag = $flag && $datahelper->isVaildIP($clientInfo['userip'],$ips);			

			$flag = ($filters['checksum'] === $datahelper::getFileChecksum($path,$user));



			if ($flag) {
				$latestSend = time();
				Data::storeMail('files', $subject, $subjectParams, $user, $type, $latestSend);
			}
		}

	}

	/**
	 * @brief Sharing a file or folder via link/public
	 * @param array $params The hook params
	 */
	public static function shareFileOrFolder($params) {
		$path = \OC\Files\Filesystem::getPath($params['fileSource']);
		$link = \OCP\Util::linkToAbsolute('files', 'index.php', array(
			'dir' => ($params['itemType'] === 'file') ? dirname($path) : $path,
		));

		Data::send('files', 'shared_link', array($path,\OCP\User::getUser()), $path, \OCP\User::getUser(), Data::TYPE_SHARED);
	}
}

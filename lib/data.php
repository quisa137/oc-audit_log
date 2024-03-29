<?php

/**
 * ownCloud - Audit_log App
 *
 * @author Frank Karlitschek
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

use \OCP\DB;
use \OCP\User;
use \OCP\Util;

/**
 * @brief Class for managing the data in the activities
 */
class Data {
    const TYPE_SHARED = 'shared';
    const TYPE_SHARE_EXPIRED = 'share_expired';
    const TYPE_SHARE_UNSHARED = 'share_unshared';
    const TYPE_SHARE_CREATED = 'file_created';
    const TYPE_SHARE_CHANGED = 'file_changed';
    const TYPE_SHARE_DELETED = 'file_deleted';
    const TYPE_SHARE_RESHARED = 'file_reshared';
    const TYPE_SHARE_RESTORED = 'file_restored';
    const TYPE_SHARE_DOWNLOADED = 'file_downloaded';
    const TYPE_SHARE_UPLOADED = 'file_uploaded';
    const TYPE_STORAGE_QUOTA_90 = 'storage_quota_90';
    const TYPE_STORAGE_FAILURE = 'storage_failure';

    /**
    * @var \OCP\Audit_log\IManager
    */
    protected $activityManager;
    public function __construct(\OCP\Audit_log\IManager $activityManager) {
        $this->activityManager = $activityManager;
    }
    protected $notificationTypes = array ();

    /**
    *
    * @param \OC_L10N $l
    * @return array Array "stringID of the type" => "translated string description
    *         for the setting"
    */
    public function getNotificationTypes(\OC_L10N $l) {
        if (isset ( $this->notificationTypes [$l->getLanguageCode ()] )) {
            return $this->notificationTypes [$l->getLanguageCode ()];
        }

        $notificationTypes = array (
            self::TYPE_SHARED => $l->t ( 'A file or folder has been <strong>shared</strong>' ),

            // self::TYPE_SHARE_UNSHARED => $l->t('Previously shared file or folder has
            // been <strong>unshared</strong>'),
            // self::TYPE_SHARE_EXPIRED => $l->t('Expiration date of shared file or
            // folder <strong>expired</strong>'),
            self::TYPE_SHARE_CREATED => $l->t ( 'A new file or folder has been <strong>created</strong>' ),
            self::TYPE_SHARE_CHANGED => $l->t ( 'A file or folder has been <strong>changed</strong>' ),
            self::TYPE_SHARE_DELETED => $l->t ( 'A file or folder has been <strong>deleted</strong>' ),

            // self::TYPE_SHARE_RESHARED => $l->t('A file or folder has been
            // <strong>reshared</strong>'),
            self::TYPE_SHARE_RESTORED => $l->t ( 'A file or folder has been <strong>restored</strong>' ),
            self::TYPE_SHARE_DOWNLOADED => $l->t ( 'A file or folder shared via link has been <strong>downloaded</strong>' )
            )
        // self::TYPE_SHARE_UPLOADED => $l->t('A file has been
        // <strong>uploaded</strong> into a folder shared via link'),
        // self::TYPE_STORAGE_QUOTA_90 => $l->t('<strong>Storage usage</strong> is at
        // 90%%'),
        // self::TYPE_STORAGE_FAILURE => $l->t('An <strong>external storage</strong>
        // has an error'),
;

        // Allow other apps to add new notification types
        $additionalNotificationTypes = $this->activityManager->getNotificationTypes($l->getLanguageCode());
        $notificationTypes = array_merge ($notificationTypes, $additionalNotificationTypes);

        $this->notificationTypes[$l->getLanguageCode()] = $notificationTypes;

        return $notificationTypes;
    }

    /**
    * Send an event into the activity stream
    *
    * @param string $app
    *         The app where this event is associated with
    * @param string $subject
    *         A short description of the event
    * @param array $subjectparams
    *         Array with parameters that are filled in the subject
    * @param string $file
    *         The file including path where this event is associated with.
    *         (optional)
    * @param string $link
    *         A link where this event is associated with (optional)
    * @param string $affecteduser
    *         If empty the current user will be used
    * @param string $type
    *         Type of the notification
    * @return bool
    */
    public static function send($app, $subject, $subjectparams = array(), $file = '', $affecteduser = '', $type = '',$clientInfo = null) {
        $timestamp = time();
        $user = User::getUser();

        if ($affecteduser === '') {
            $auser = $user;
        } else {
            $auser = $affecteduser;
        }
        if(!$clientInfo) {
            $dh = new DataHelper();
            $clientInfo = $dh->parseUserAgent();
            if($type !== Data::TYPE_SHARE_DELETED){
                $clientInfo['filesize'] = $dh->getFileSize($file,$auser);
                $clientInfo['checksum'] = $dh->getFileChecksum($file,$auser);
            }
        }

        // store in DB
        $query = DB::prepare ( 'INSERT INTO `*PREFIX*audit_log`(`app`, `subject`, `subjectparams`, `file`,`filesize`, `user`, `affecteduser`, `timestamp`, `type`, `userip`, `device`, `os`, `browser`, `userAgent`, `checksum`)' .
            ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, inet_aton(?), ?, ?, ?, ?, ?)' );
        $query->execute(array($app,$subject,serialize($subjectparams),$file,$clientInfo['filesize'],$user,$auser,$timestamp,$type,$clientInfo['userip'],$clientInfo['device'],$clientInfo['os'],$clientInfo['browser'],$clientInfo['userAgent'],$clientInfo['checksum']));

        return true;
    }

    /**
    * @brief Send an event into the activity stream
    *
    * @param string $app
    *         The app where this event is associated with
    * @param string $subject
    *         A short description of the event
    * @param array $subjectParams
    *         Array of parameters that are filled in the placeholders
    * @param string $affectedUser
    *         Name of the user we are sending the activity to
    * @param string $type
    *         Type of notification
    * @param int $latestSendTime
    *         Audit_log time() + batch setting of $affecteduser
    * @return bool
    */
    public static function storeMail($app, $subject, array $subjectParams, $affectedUser, $type, $latestSendTime) {
        $timestamp = time ();

    // store in DB
        $query = DB::prepare ( 'INSERT INTO `*PREFIX*audit_log_mq` ' .
            ' (`amq_appid`, `amq_subject`, `amq_subjectparams`, `amq_affecteduser`, `amq_timestamp`, `amq_type`, `amq_latest_send`) ' .
            ' VALUES(?, ?, ?, ?, ?, ?, ?)' );
        $query->execute(array(
            $app,$subject,serialize ( $subjectParams ),$affectedUser,$timestamp,$type,
            $latestSendTime));
        return true;
    }

/**
* Filter the activity types
*
* @param array $types
* @param string $filter
* @return array
*/
public function filterNotificationTypes($types, $filter) {
    switch ($filter) {
        case 'shares' :
        return array_intersect ( array (
            Data::TYPE_SHARED
            ), $types );
    }

// Allow other apps to add new notification types
    return $this->activityManager->filterNotificationTypes ( $types, $filter );
}

/**
* @brief Read a list of events from the activity stream
*
* @param GroupHelper $groupHelper
*         Allows activities to be grouped
* @param int $start
*         The start entry
* @param int $count
*         The number of statements to read
* @param string $filter
*         Filter the activities
* @return array
*/
public function read(GroupHelper $groupHelper, $start, $count, $filter = 'all',$filterValue = '', $searchOption = array()) {
// get current user
    $user = User::getUser();
    $enabledNotifications = UserSettings::getNotificationTypes ( $user, 'stream' );
    $enabledNotifications = $this->filterNotificationTypes ( $enabledNotifications, $filter );

// We don't want to display any activities
    if (empty ( $enabledNotifications )) {
        return array();
    }

    $whereColumns = '';
    $sqlWhereQueries = $parameters = array();
    $limitActivities = " AND `type` IN ('" . implode ( "','", $enabledNotifications ) . "')";

    if(!empty($searchOption)) {
        foreach($searchOption as $k => $v) {
            switch ($k) {
                case 'stdDate':
                $sqlWhereQueries[] = ' AND unix_timestamp(?) <= timestamp';
                $parameters[] = $v;
                break;
                case 'endDate':
                $sqlWhereQueries[] = ' AND timestamp <= unix_timestamp(?)';
                $parameters[] = $v;
                break;
                case 'fileName':
                $sqlWhereQueries[] = ' AND file like ? ';
                $v = '%'.$v;
                $parameters[] = $v;
                break;
                case 'userIP':
                $sqlWhereQueries[] = ' AND userip = inet_aton(?)';
                $parameters[] = $v;
                break;
                case 'user':
                case 'checksum':
                $sqlWhereQueries[] = ' AND '.$k.' = ?';
                $parameters[] = $v;
                break;
                case 'os':
                case 'device':
                case 'types':
                $values = explode(' ', $v);
                $tmpWhereQuery = '';
                foreach ($values as $value) {
                    if($value ==='OSX') $value = '%OS X';
                    $tmpWhereQuery .= !empty($tmpWhereQuery)?' OR ':'';
                    $tmpWhereQuery .= $k.' like ?';
                    $parameters[] = $value.($value !== '%OS X'?'%':'');
                }
                $sqlWhereQueries[] = ' AND ('.$tmpWhereQuery.')';

                break;
            }
        }
    }

// fetch from DB
    $query = DB::prepare('SELECT *,inet_ntoa(userip) as userip ' .
        ' FROM `*PREFIX*audit_log` WHERE 1=1 ' . implode(' ', $sqlWhereQueries) .
        $limitActivities . ' ORDER BY `timestamp` DESC', $count, $start);
    $result = $query->execute($parameters);

    return $this->getActivitiesFromQueryResult($result, $groupHelper);
}

/**
* Get Devices
*/
public function getDevices() {
    $query = DB::prepare('select distinct device from oc_owncloud.oc_audit_log where device is not null');
    $result = $query->execute(array());
    if(DB::isError($result)) {
        Util::writeLog('Audit_log', DB::getErrorMessage($result), Util::ERROR);
        return array();
    } else {
        return $result->fetchAll();
    }
}

/**
* Get Types
*/
public function getTypes() {
    $query = DB::prepare('select distinct type from oc_audit_log where type is not null');
    $result = $query->execute(array());
    if(DB::isError($result)) {
        Util::writeLog('Audit_log', DB::getErrorMessage($result), Util::ERROR);
        return array();
    } else {
        return $result->fetchAll();
    }
}

/**
* Process the result and return the activities
*
* @param \OC_DB_StatementWrapper|int $result
* @param \OCA\Audit_log\GroupHelper $groupHelper
* @return array
*/
public function getActivitiesFromQueryResult($result, GroupHelper $groupHelper) {
    if (DB::isError($result)) {
        Util::writeLog('Audit_log', DB::getErrorMessage($result), Util::ERROR);
    } else {
        while ($row = $result->fetchRow()) {
            $groupHelper->addAudit_log($row);
        }
    }

    return $groupHelper->getActivities();
}

/**
* Get the casted page number from $_GET
*
* @return int
*/
public function getPageFromParam() {
    if (isset($_GET['page'])) {
        return (int)$_GET['page'];
    }

    return 1;
}

/**
* Get grouping from $_GET
* 
* @return boolean
*/
public function getGroupingFromParam() {
    if(!isset($_GET['grouping']))
        return true;
    $grouping = $_GET['grouping'];
    return ($grouping=='true')?true:false;
}

/**
* Get the SearchOption from $_GET
*
* @return array
*/
public function getSearchOptionFromParam($paramList) {
    $result = array();
    foreach($paramList as $param) {
        $paramValue = $_GET[$param];
        if(!empty($paramValue)) {
            $result[$param] = $paramValue;
        }
    }
    return $result;
}
/**
* Get the filter from $_GET
*
* @return array
*/
public function getFilterFromParam() {
    if (!isset($_GET['filter']))
        return array();

    $filterValue = $_GET['filter'];
    switch ($filterValue) {
        case 'redZone':
        case 'fileHistory':
        return $filterValue;
        default:
        if($this->activityManager->isFilterValid($filterValue)) {
            return $filterValue;
        }
        return 'all';
    }
}

/**
* Delete old events
*
* @param int $expireDays
*         Minimum 1 day
* @return null
*/
public function expire($expireDays = 365) {
    $ttl = (60 * 60 * 24 * max(1, $expireDays));

    $timelimit = time() - $ttl;
    $this->deleteActivities(array('timestamp' => array($timelimit,'<')));
}

/**
* Delete activities that match certain conditions
*
* @param array $conditions
*         Array with conditions that have to be met
*         'field' => 'value' => `field` = 'value'
*         'field' => array('value', 'operator') => `field` operator 'value'
* @return null
*/
public function deleteActivities($conditions) {
    $sqlWhere = '';
    $sqlParameters = $sqlWhereList = array ();
    foreach ($conditions as $column => $comparison) {
        $sqlWhereList[] = " `$column` " . ((is_array($comparison) && isset($comparison[1]))?$comparison[1]:'=').' ? ';
        $sqlParameters[] = (is_array($comparison))?$comparison[0]:$comparison;
    }

    if (!empty($sqlWhereList)) {
        $sqlWhere = ' WHERE ' . implode(' AND ', $sqlWhereList);
    }

    $query = DB::prepare('DELETE FROM `*PREFIX*activity`' . $sqlWhere);
    $query->execute($sqlParameters);
}
}

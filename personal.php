<?php

/**
* ownCloud - Audit_log App
*
* @author Joas Schilling
* @copyright 2014 Joas Schilling nickvergessen@owncloud.com
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
*/

\OCP\Util::addScript('audit_log', 'settings');
\OCP\Util::addStyle('audit_log', 'settings');

$l = \OCP\Util::getL10N('audit_log');
$data = new \OCA\Audit_log\Data(\OC::$server->getAudit_logManager());
$types = $data->getNotificationTypes($l);

$user = \OCP\User::getUser();
$activities = array();
foreach ($types as $type => $desc) {
	$activities[$type] = array(
		'desc'		=> $desc,
		'email'		=> \OCA\Audit_log\UserSettings::getUserSetting($user, 'email', $type),
		'stream'	=> \OCA\Audit_log\UserSettings::getUserSetting($user, 'stream', $type),
	);
}

$template = new \OCP\Template('audit_log', 'personal');
$template->assign('activities', $activities);
if (\OCA\Audit_log\UserSettings::getUserSetting($user, 'setting', 'batchtime') == 3600 * 24 * 7) {
	$template->assign('setting_batchtime', \OCA\Audit_log\UserSettings::EMAIL_SEND_WEEKLY);
}
else if (\OCA\Audit_log\UserSettings::getUserSetting($user, 'setting', 'batchtime') == 3600 * 24) {
	$template->assign('setting_batchtime', \OCA\Audit_log\UserSettings::EMAIL_SEND_DAILY);
}
else {
	$template->assign('setting_batchtime', \OCA\Audit_log\UserSettings::EMAIL_SEND_HOURLY);
}
$template->assign('activity_email', \OCP\Config::getUserValue($user, 'settings', 'email', ''));
$template->assign('notify_self', \OCA\Audit_log\UserSettings::getUserSetting($user, 'setting', 'self'));

return $template->fetchPage();

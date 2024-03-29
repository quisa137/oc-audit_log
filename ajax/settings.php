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
*
*/

\OCP\JSON::checkLoggedIn();
\OCP\JSON::checkAppEnabled('audit_log');
\OCP\JSON::callCheck();

$notify_email = $notify_stream = array();

$l = \OCP\Util::getL10N('audit_log');
$data = new \OCA\Audit_log\Data(\OC::$server->getActivityManager());
$types = $data->getNotificationTypes($l);
foreach ($types as $type => $desc) {
	\OCP\Config::setUserValue(\OCP\User::getUser(), 'audit_log', 'notify_email_' . $type, !empty($_POST[$type . '_email']));
	\OCP\Config::setUserValue(\OCP\User::getUser(), 'audit_log', 'notify_stream_' . $type, !empty($_POST[$type . '_stream']));
}

$email_batch_time = 3600;
if ($_POST['notify_setting_batchtime'] == \OCA\Audit_log\UserSettings::EMAIL_SEND_DAILY) {
	$email_batch_time = 3600 * 24;
}
if ($_POST['notify_setting_batchtime'] == \OCA\Audit_log\UserSettings::EMAIL_SEND_WEEKLY) {
	$email_batch_time = 3600 * 24 * 7;
}
\OCP\Config::setUserValue(\OCP\User::getUser(), 'audit_log', 'notify_setting_batchtime', $email_batch_time);
\OCP\Config::setUserValue(\OCP\User::getUser(), 'audit_log', 'notify_setting_self', !empty($_POST['notify_setting_self']));

\OCP\JSON::success(array("data" => array( "message" => $l->t('Your settings have been updated.'))));

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

// some housekeeping
\OCP\JSON::checkLoggedIn();
\OCP\JSON::checkAppEnabled('audit_log');
\OCP\JSON::setContentTypeHeader();
$data = new \OCA\Audit_log\Data(\OC::$server->getActivityManager());
$devices = $data->getDevices();
echo \OC_JSON::encode($devices);
?>
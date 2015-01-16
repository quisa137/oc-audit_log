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

/**
 * @var $this OC\Route\Router
 */
$this->create('audit_log.index', '')->actionInclude('audit_log/index.php');
$this->create('audit_log.fileHistory', '')->actionInclude('audit_log/index.php');
$this->create('audit_log.userHistory', '')->actionInclude('audit_log/index.php');
$this->create('audit_log.ipHistory', '')->actionInclude('audit_log/index.php');
$this->create('audit_log.ajax.fetch', 'ajax/fetch.php')->actionInclude('audit_log/ajax/fetch.php');
$this->create('audit_log.ajax.fetch_devices', 'ajax/fetch_devices.php')->actionInclude('audit_log/ajax/fetch_devices.php');
$this->create('audit_log.ajax.fetch_types', 'ajax/fetch_types.php')->actionInclude('audit_log/ajax/fetch_types.php');
// $this->create('audit_log.ajax.rssfeed', 'ajax/rssfeed.php')->actionInclude('audit_log/ajax/rssfeed.php');
$this->create('audit_log.ajax.settings', 'ajax/settings.php')->actionInclude('audit_log/ajax/settings.php');
$this->create('audit_log.ajax.getDialog', 'ajax/getDialog.php')->actionInclude('audit_log/ajax/getDialog.php');

// Register an OCS API call
// OC_API::register(
// 	'get',
// 	'/cloud/audit_log',
// 	array('OCA\Audit_log\Api', 'get'),
// 	'audit_log'
// );

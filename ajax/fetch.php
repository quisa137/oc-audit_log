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

$l = \OCP\Util::getL10N('audit_log');
$data = new \OCA\Audit_log\Data(\OC::$server->getActivityManager());

$page = $data->getPageFromParam() - 1;
$searchOption = $data->getSearchOptionFromParam(array('stdDate','endDate', 'fileName', 'userIP', 'user', 'os', 'device','checksum','types'));
$filter = $data->getFilterFromParam();
$filterValue = '';
$grouping = $data->getGroupingFromParam();

$groupHelper = new \OCA\Audit_log\GroupHelper(
    \OC::$server->getActivityManager(),
    new \OCA\Audit_log\DataHelper(\OC::$server->getActivityManager(),
        new \OCA\Audit_log\ParameterHelper(
            new \OC\Files\View(''), $l
        ), $l
    ), $grouping
);


// Read the next 30 items for the endless scrolling
$count = 100;
$activity = $data->read($groupHelper, $page * $count, $count, $filter, $filterValue, $searchOption);

// show the next 30 entries
$tmpl = new \OCP\Template('audit_log', 'activities.part', '');
$tmpl->assign('audit_log', $activity);
$tmpl->printPage();

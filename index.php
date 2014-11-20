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


// check if the user has the right permissions to access the activities
\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('audit_log');

// activate the right navigation entry
\OCP\App::setActiveNavigationEntry('audit_log');

// load the needed js scripts and css
\OCP\Util::addScript('audit_log', 'script');
\OCP\Util::addStyle('audit_log', 'style');

$navigation = new \OCA\Audit_log\Navigation(\OCP\Util::getL10N('audit_log'), \OC::$server->getActivityManager(), \OC::$server->getURLGenerator());
$navigation->setRSSToken(\OCP\Config::getUserValue(\OCP\User::getUser(), 'audit_log', 'rsstoken'));

// get the page that is requested. Needed for endless scrolling
$data = new \OCA\Audit_log\Data(
	\OC::$server->getAudit_logManager()
);
$page = $data->getPageFromParam() - 1;
$filter = $data->getFilterFromParam();

// show activity template
$tmpl = new \OCP\Template('audit_log', 'list', 'user');
$tmpl->assign('filter', $filter);
$tmpl->assign('appNavigation', $navigation->getTemplate($filter));
$tmpl->printPage();
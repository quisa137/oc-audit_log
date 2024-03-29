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
$l = OC_L10N::get('audit_log');

if(\OC_User::isAdminUser(\OC_User::getUser())) {
    // TODO : 현재는 관리자일때만 보이기, 지정한 사용자 일 때만, 보이기
    OCP\App::addNavigationEntry(array(
    'id' => 'audit_log',
    'href' => OCP\Util::linkToRoute('audit_log.index'),
    'icon' => OCP\Util::imagePath('audit_log', 'audit_log.svg'),
    'name' => $l->t('Audit_log')
    ));
}

// register the hooks for filesystem operations. All other events from other
// apps has to be send via the public api
OCA\Audit_log\Hooks::register();

// Admin settings for notifications and emails
OCP\App::registerAdmin('audit_log', 'admin');

// Cron job for sending Emails
OCP\Backgroundjob::registerJob('OCA\Audit_log\BackgroundJob\EmailNotification');

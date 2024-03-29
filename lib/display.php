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
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\Audit_log;

/**
 * Class Display
 *
 * @package OCA\Audit_log
 */
class Display {
 /**
  * Get the template for a specific activity-event in the activities
  *
  * @param array $activity
  *         An array with all the activity data in it
  * @param
  *         return string
  */
 public static function show($activity,$grouping) {
  $tmpl = new \OCP\Template('audit_log', 'activity.box');
  $tmpl->assign('formattedDate',\OCP\Util::formatDate($activity['timestamp']));
  $tmpl->assign('formattedTimestamp',\OCP\relative_modified_date($activity['timestamp']));
  $tmpl->assign('user', $activity['user']);
  $tmpl->assign('displayName', \OCP\User::getDisplayName($activity['user']));

  if ($activity['app'] === 'files') {
   // We do not link the subject as we create links for the parameters instead
   $activity['link'] = '';
  }
  if(is_array($activity['subjectparams'][0])) {
  	$activity['subjecttitle'] = implode('<br>', $activity['subjectparams'][0]);
  }else{
  	$activity['subjecttitle'] = $activity['subjectparams'][0];
  }

  $tmpl->assign('event', $activity);
  $tmpl->assign('grouping',$grouping);

//   if ($activity ['file']) {
//    $rootView = new \OC\Files\View('');
//    $exist = $rootView->file_exists('/' . $activity['user'] . '/files' . $activity['file']);
//    $is_dir = $rootView->is_dir('/' . $activity['user'] . '/files' . $activity['file']);
//    unset($rootView);

//    // show a preview image if the file still exists
//    if (!$is_dir && $exist) {
//     $tmpl->assign(
//     	'previewLink',
//     	\OCP\Util::linkTo(
//     		'files', 'index.php', array('dir' => dirname($activity['file']))
//     	)
//     );
//     $tmpl->assign(
//     	'previewImageLink',
//     	\OCP\Util::linkToRoute(
//     		'core_ajax_preview',
//     		array('file' => $activity['file'],'x' => 150,'y' => 150)
//     	)
//     );
//    } else if ($exist) {
//     $tmpl->assign(
//     	'previewLink',
//     	\OCP\Util::linkTo(
//     		'files', 'index.php', array('dir' => $activity['file'])
//     	)
//     );
//     $tmpl->assign('previewImageLink', \OC_Helper::mimetypeIcon('dir'));
//     $tmpl->assign('previewLinkIsDir', true );
//    }
//   }

  return $tmpl->fetchPage();
 }
}

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

use \OCP\Util;
use \OCP\Audit_log\IManager;

class DataHelper {
 /**
  * @var \OCP\Audit_log\IManager
  */
 protected $activityManager;

 /**
  * @var \OCA\Audit_log\ParameterHelper
  */
 protected $parameterHelper;

 /**
  * @var \OC_L10N
  */
 protected $l;
 public function __construct(IManager $activityManager, ParameterHelper $parameterHelper, \OC_L10N $l) {
  $this->activityManager = $activityManager;
  $this->parameterHelper = $parameterHelper;
  $this->l = $l;
 }

 /**
  * @brief Translate an event string with the translations from the app where it
  * was send from
  *
  * @param string $app
  *         The app where this event comes from
  * @param string $text
  *         The text including placeholders
  * @param array $params
  *         The parameter for the placeholder
  * @param bool $stripPath
  *         Shall we strip the path from file names?
  * @param bool $highlightParams
  *         Shall we highlight the parameters in the string?
  *         They will be highlighted with `<strong>`, all data will be passed
  *         through
  *         \OCP\Util::sanitizeHTML() before, so no XSS is possible.
  * @return string translated
  */
 public function translation($app, $text, $params, $stripPath = false, $highlightParams = false) {
  if (! $text) {
   return '';
  }

  if ($app === 'files') {
   $preparedParams = $this->parameterHelper->prepareParameters($params, $this->parameterHelper->getSpecialParameterList($app,$text), $stripPath, $highlightParams);
   return $preparedParams[0];
  }

  // Allow other apps to correctly translate their activities
  $translation = $this->activityManager->translate($app,$text,$params,$stripPath,$highlightParams,$this->l->getLanguageCode());

  if ($translation !== false) {
   return $translation;
  }

  $l = Util::getL10N($app);
  return $l->t($text,$params);
 }

 /**
  * Format strings for display
  *
  * @param array $activity
  * @param string $message
  *         'subject' or 'message'
  * @return array Modified $activity
  */
 public function formatStrings($activity, $message) {
  $activity[$message . 'params'] = $activity[$message . 'params_array'];
  unset($activity[$message . 'params_array']);

  $activity[$message . 'formatted'] = array(
    'trimmed' => $this->translation($activity['app'],$activity[$message],$activity[$message . 'params'], true),
    'full' => $this->translation($activity['app'],$activity[$message],$activity[$message . 'params']),
    'markup' => array (
     'trimmed' => $this->translation($activity['app'],$activity[$message],$activity[$message . 'params'], true, true),
     'full' => $this->translation($activity['app'],$activity[$message],$activity[$message . 'params'], false, true)
    )
  );
  return $activity;
 }

 /**
  * Get the icon for a given activity type
  *
  * @param string $type
  * @return string CSS class which adds the icon
  */
 public function getTypeIcon($type) {
  switch ($type) {
   case Data::TYPE_SHARE_CHANGED :
    return 'glyphicon-refresh';
   case Data::TYPE_SHARE_CREATED :
    return 'glyphicon-cloud-upload';
   case Data::TYPE_SHARE_DOWNLOADED :
       return 'glyphicon-cloud-download';
   case Data::TYPE_SHARE_DELETED :
    return 'glyphicon-remove';
   case Data::TYPE_SHARED :
    return 'glyphicon-transfer';
   case Data::TYPE_SHARE_RESTORED :
       return 'glyphicon-repeat';
  }

  // Allow other apps to add a icon for their notifications
  return $this->activityManager->getTypeIcon($type);
 }

/**
 * Parse the User Agent
 * Device, DeviceType, OSType,IP, Browser String, Connect Region
 */
public function parseUserAgent() {
    $uaStr = $_SERVER['HTTP_USER_AGENT'];
    $uIP = \OC_Util::getUserIP();
    $uIP = ($uIP==='::1')?'127.0.0.1':$uIP;
    $osRegex = '/Windows( NT| Phone| CE)?|Mac OS X|Android( \d(\.\d(\.\d)?)?)?/';
    $isLinuxRegex = '/Linux( arm| x86_64| i686)?/';
    $deviceRegex = '/Macintosh|iPad|iPhone|BlackBerry|Samsung|LG|HTC|Android/';
    $browserRegex = '/MSIE( \d{1,3}\.\d)?|Mobile Safari|Chrome|Firefox|Safari|mirall|neon/';
    $ie11LaterRegex = '/(Trident)\/.+; rv:(\d{1,3}\.\d)/';
    $device = array();
    $os = array();
    $browser = array();
    $linux = array();

    preg_match($deviceRegex, $uaStr, $device);
    preg_match($osRegex, $uaStr, $os);
    preg_match($browserRegex, $uaStr,$browser);
    $isLinux = preg_match($isLinuxRegex, $uaStr, $linux);
    $os = count($os)>0?$os[0]:'';
    $browser = count($browser)>0?$browser[0]:'';
    $device = count($device)>0?$device[0]:'';

    if(empty($os)){
        if($isLinux){
            $os = count($linux)>0?$linux[0]:'unknown linux';
        }else{
            $os = 'unknown';
        }
    }
    if(empty($browser)){
        if(preg_match($ie11LaterRegex, $uaStr,$browser)){
            if(count($browser)==3){
                $tmpBrowser = $browser[1]=='Trident'?'MSIE':'';
                $tmpBrowser .= ' '.(floatval($browser[2]));
                $browser = $tmpBrowser;
            }
        }else{
            $browser = 'unknown';
        }
    }
    if(empty($device)){
        $device = 'PC';
    }
  $browser = ($browser == 'neon')?'mirall':$browser;
  $os = ($device == 'Macintosh')?'MAC OS X':$os;

    $result = array(
        'os' => $os,
        'browser' => $browser,
        'device' => $device,
        'userip' => $uIP,
    'userAgent' => $uaStr
    );
    return $result;
}

 /**
  * Get File checksum
  */
 public function getFileChecksum($filename) {
     $absolutePath = \OC_User::getHome(\OC_User::getUser()) . '/files' . $filename;

     if(file_exists($absolutePath)) {
         return md5_file($absolutePath, false);
     }
     return '';
 }
 /**
  * Get File checksum
  */
 public function getFileSize($filename) {
     $absolutePath = \OC_User::getHome(\OC_User::getUser()) . '/files' . $filename;

     if(file_exists($absolutePath)) {
         return filesize($absolutePath);
     }
     return 0;
 }
}

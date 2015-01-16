<?php
namespace OCA\Audit_log;

use \OCP\Config;

/**
 * Class UserSettings
 *
 * @package OCA\Audit_log
 */
class Settings {
    const APP_NAME = 'audit_log';
    protected static function getAppValue($key, $default){
        return \OCP\Config::getAppValue(self::APP_NAME, $key, $default);
    }

    protected static function setAppValue($key, $value){
        return \OCP\Config::setAppValue(self::APP_NAME, $key, $value);
    }
    public static function setFilters($filters = array()){
        return self::setAppValue('filters', serialize($filters));
    }
    public static function getFilters(){
        return unserialize(self::getAppValue('filters',array()));
    }
}
?>
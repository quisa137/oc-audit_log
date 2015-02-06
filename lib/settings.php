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
    public static function setFilters($filters = ''){
        return self::setAppValue('filters', $filters);
    }
    public static function getFilters(){
        return self::getAppValue('filters','');
    }
    public static function getFilterstoArray(){
        json_decode(self::getFilters());
    }
    public static function setDefaultEmails($defaultEmails = '') {
        return self::setAppValue('defaultEmails',$defaultEmails);
    }
    public static function getDefaultEmails() {
        return self::getAppValue('defaultEmails','');
    }
}
?>
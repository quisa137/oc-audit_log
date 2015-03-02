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
    public static function encryptText($plaintext){
        $key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,$plaintext, MCRYPT_MODE_CBC, $iv);

        # prepend the IV for it to be available for decryption
        $ciphertext = $iv . $ciphertext;
        
        # encode the resulting cipher text so it can be represented by a string
        return base64_encode($ciphertext);
    }
    public static function decryptText($ciphertext){
        $key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
        $ciphertext_dec = base64_decode($ciphertext);

        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);
        
        # retrieves the cipher text (everything except the $iv_size in the front)
        $ciphertext_dec = substr($ciphertext_dec, $iv_size);

        # may remove 00h valued characters from end of plain text
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
                                    $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
    }
    public static function getUserIP() {
        if (isset($_SERVER)) { 
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) 
                return $_SERVER["HTTP_X_FORWARDED_FOR"]; 
            if (isset($_SERVER["HTTP_CLIENT_IP"])) 
                return $_SERVER["HTTP_CLIENT_IP"]; 
            return $_SERVER["REMOTE_ADDR"];
        }
        if (getenv('HTTP_X_FORWARDED_FOR')) 
            return getenv('HTTP_X_FORWARDED_FOR'); 
        if (getenv('HTTP_CLIENT_IP')) 
            return getenv('HTTP_CLIENT_IP'); 
        return getenv('REMOTE_ADDR');
    }       
}
?>
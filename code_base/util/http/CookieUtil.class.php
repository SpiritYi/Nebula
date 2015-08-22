<?php
/**
 * 重新实现cookie 操作类，加密处理
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/08/20
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/util/secret/AesEncrypt.class.php';

class CookieUtil {
    /**
     * 写cookie
     * @param $lifeTime int     //保留时间, 单位s
     */
    public static function write($key, $value, $lifeTime = 3600) {
        $encValue = self::_encryptCookie($value);
        setcookie($key, $encValue, time() + $lifeTime);
    }

    //返回一个cookie 信息, ajax 保存cookie 使用
    public static function create($key, $value, $lifeTime = 3600) {
        return array(
            'k' => $key,
            'v' => self::_encryptCookie($value),
            't' => time() + $lifeTime,
            't_h' => date('Y/m/d H:i:s', time() + $lifeTime),
        );
    }

    public static function read($key) {
        if (!isset($_COOKIE[$key]))
            return false;
        $encryptStr = $_COOKIE[$key];
        return self::_decryptCookie($encryptStr);
    }

    //还原cookie 存储的字符串
    public static function reduce($value) {
        return self::_decryptCookie($value);
    }

    public static function delete($key) {
        setcookie($key, '', time() - 10);
    }

    private static function _encryptCookie($value) {
        return base64_encode(AesEncrypt::encrypt($value));
    }
    private static function _decryptCookie($str) {
        return AesEncrypt::decrypt(base64_decode($str));
    }
}

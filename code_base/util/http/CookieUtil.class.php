<?php
/**
 * 重新实现cookie 操作类，加密处理
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/08
 * @copyright nebula.com
 */

require_once CODE_BASE . '/util/secret/AesEcb.class.php';

class CookieUtil {
    /**
     * 写cookie
     * @param $lifeTime int     //保留时间, 单位s
     */
    public static function write($key, $value, $lifeTime = 3600) {
        $encValue = AesEcb::encrypt($value);
        setcookie($key, $encValue, time() + $lifeTime);
    }

    public static function read($key) {
        if (!isset($_COOKIE[$key]))
            return false;
        $encryptStr = $_COOKIE[$key];
        return AesEcb::decrypt($encryptStr);
    }

    public static function delete($key) {
        setcookie($key, '', time() - 10);
    }
}
<?php
/**
 * openssl 的aes 加密
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/20
 * @copyright nebula-fund.com
 */

require_once CONFIG . '/DBConfig.class.php';

class AesEncrypt {

    const AES_METHOD = 'aes-256-cbc';

    /**
     * aes 加密函数
     * @param $data string //需要加密的数据
     * @param $key string   //默认使用
     * @return string
     */
    public static function encrypt($data, $key = '') {
        if (empty($key)) {
            $key = DBConfig::OPENSSL_AES_KEY;
        }
        $encStr = openssl_encrypt($data, self::AES_METHOD, $key, OPENSSL_RAW_DATA, DBConfig::OPENSSL_AES_IV);
        return $encStr;
    }

    public static function decrypt($data, $key = '') {
        if (empty($key)) {
            $key = DBConfig::OPENSSL_AES_KEY;
        }
        $decStr = openssl_decrypt($data, self::AES_METHOD, $key, OPENSSL_RAW_DATA, DBConfig::OPENSSL_AES_IV);
        return $decStr;
    }
}
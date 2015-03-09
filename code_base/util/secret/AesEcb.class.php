<?php
/**
 * AES ECB 需要php 扩展，这里暂时使用网上提供的简易对称加密
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/08
 * @copyright nebula.com
 */

require_once CONFIG . '/DBConfig.class.php';

class AesEcb {
    public static function test() {
        $string = 'sfs"sdfs"[]<>?,..,';

        // encrypt $string, and store it in $enc_text
        $enc_text = self::encrypt($string);

        // decrypt the encrypted text $enc_text, and store it in $dec_text
        $dec_text = self::decrypt($enc_text);

        print "加密的 text : $enc_text <Br> ";
        print "解密的 text : $dec_text <Br> ";
    }

    public static function encrypt($txt) {
        srand((double)microtime() * 1000000);
        $encrypt_key = md5(rand(0, 32000));
        $ctr = 0;
        $tmp = "";
        for ($i = 0; $i < strlen($txt); $i ++) {
            if ($ctr == strlen($encrypt_key)) $ctr=0;
            $tmp .= substr($encrypt_key, $ctr, 1) . (substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1));
            $ctr ++;
        }
        return self::_keyED($tmp, DBConfig::AESECB_KEY);
    }

    public static function decrypt($txt) {
        $txt = self::_keyED($txt, DBConfig::AESECB_KEY);
        $tmp = "";
        for ($i = 0; $i < strlen($txt); $i ++) {
            $md5 = substr($txt, $i, 1);
            $i ++;
            $tmp .= (substr($txt, $i, 1) ^ $md5);
        }
        return $tmp;
    }

    private static function _keyED($txt, $encrypt_key) {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = "";
        for ($i=0; $i < strlen($txt); $i ++) {
            if ($ctr == strlen($encrypt_key)) $ctr = 0;
            $tmp.= substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1);
            $ctr++;
        }
        return $tmp;
    }
}

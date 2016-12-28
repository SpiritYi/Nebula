<?php
/**
 * es 集群http 连接操作接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/24
 * @copyright nebula-fund.com
 */

require_once CONFIG . '/DBConfig.class.php';

class EsUtil {

    public static function curlRequest($url, $method, $data = array()) {
        if (!empty($data) && !is_array($data)) {        //有数据情况,传字典数组
            return false;
        }
        ksort($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
//         curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        // curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, DBConfig::ES_AUTH_USER);      //接口调用认证
        // curl_setopt($ch, CURLOPT_AUTOREFERER,true);
        // curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);

        $result = curl_exec($ch);
//        $info = curl_getinfo($ch);
        curl_close($ch);
        $resultArr = json_decode($result, true);
        return is_array($resultArr) ? $resultArr : false;
    }
}
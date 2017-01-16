<?php
/**
 * http 操作类
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/09
 * @copyright nebula.com
 */

class HttpUtil {
    public static $param = array();

    public static function initParams() {
        //从input 中读取传入json 串，只能读取一次
        $paramJson = json_decode(file_get_contents("php://input"), true);
        if (!empty($paramJson)) {
            self::$param = array_merge(self::$param, $paramJson);
        }
        return true;
    }

    public static function curlget($url, $header = array()) {
//        $header[] = 'Connection:Keep-Alive';
        $header[] = 'Content-Type:application/json';
//        $header[] = 'Accept:text/html';
        $header[] = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
        $header[] = 'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // curl_setopt($ch, CURLOPT_POST, 0);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        // curl_setopt($ch, CURLOPT_VERBOSE, 0);
        // curl_setopt($ch, CURLOPT_AUTOREFERER,true);
        // curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($ch);
//        $info = curl_getinfo($ch);
        curl_close($ch);

        return $result;
    }

    //获取请求参数，依次读取传递的方式
    public static function getParam($name, $default = null) {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        if (isset(self::$param[$name])) {
            return self::$param[$name];
        }
        if (isset($_SERVER['HTTP_' . strtoupper($name)])) {
            return $_SERVER['HTTP_' . strtoupper($name)];
        }
        return $default;
    }
}

class HttpUtilEx extends HttpUtil {

}

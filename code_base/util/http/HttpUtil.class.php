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

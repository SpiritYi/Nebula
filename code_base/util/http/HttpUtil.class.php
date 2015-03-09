<?php
/**
 * http 操作类
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/09
 * @copyright nebula.com
 */

class HttpUtil {
    //获取请求参数，依次读取传递的方式
    public static function getParam($name) {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        if (isset($_SERVER['HTTP_' . strtoupper($name)])) {
            return $_SERVER['HTTP_' . strtoupper($name)];
        }
        return false;
    }
}

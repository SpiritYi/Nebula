<?php
/**
 * api 接口入口文件
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/15
 * @copyright nebula.com
 */

require_once dirname(__FILE__) . '/../config/DirConfig.inc.php';
require_once CODE_BASE . '/util/logger/Logger.class.php';
require_once CODE_BASE . '/util/http/HttpUtil.class.php';

require_once API . '/assembly/config/status_errcode/BaseStatusCode.config.php';
require_once API . '/assembly/ResourceBase.class.php';

class IndexBase {
    public static function dispatch() {
        //ajax 跨域支持
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        if (isset($_SERVER['HTTP_ORIGIN']) && preg_match('/.*nebula.*/', $_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin:*');
        };

        require_once API . '/assembly/ClientRouter.class.php';
        //解析url 获取相应数据
        $uriMatch = ClientRouter::match($_SERVER['REQUEST_URI']);
        if (empty($uriMatch)) {
            ResourceBase::output(404, '', 'uri match failed.');
        }
        //支持的请求方式
        $allowMethod = array('GET', 'POST', 'PUT', 'DELETE');
        if (!in_array($uriMatch['method'], $allowMethod)) {
            if ($uriMatch['method'] == 'OPTIONS') {
                ResourceBase::output(200, $allowMethod);
            } else {
                ResourceBase::output(405, '');
            }
        }
        //根据请求分发加载resource 文件
        $resourceLoc = API . $uriMatch['location'];
        $resourcePathTemp = $resourceLoc . '/resource/%s.class.php';
        $resourceFile = sprintf($resourcePathTemp, $uriMatch['resource']);
        $allFiles = array();    //保留文件夹下所有文件大写文件名数据
        //兼容resource 的小写和服务器区分大小写文件名
        if (!file_exists($resourceFile)) {
            $scanList = scandir($resourceLoc . '/resource/');
            $fileNamePreg = '/(.*)\.class.php/';
            foreach ($scanList as $path) {
                if (preg_match($fileNamePreg, $path, $temp))
                    $allFiles[strtolower($temp[1])] = $temp[1];
            }
            $resourceFile = sprintf($resourcePathTemp, $allFiles[$uriMatch['resource']]);
        }
        if (!file_exists($resourceFile)) {
            ResourceBase::output(404, '', 'route error or resource not fount');
        }

        //加载资源
        require_once $resourceFile;
        $class = $uriMatch['resource'] . 'Res';
        if (!class_exists($class)) {    //url class 兼容大小写
            if (empty($allFiles)) {     //前面没有扫描，这里补充扫描一次
                $scanList = scandir($resourceLoc . '/resource/');
                $fileNamePreg = '/(.*)\.class.php/';
                foreach ($scanList as $path) {
                    if (preg_match($fileNamePreg, $path, $temp))
                        $allFiles[strtolower($temp[1])] = $temp[1];
                }
            }
            $class = $allFiles[$uriMatch['resource']] . 'Res';
        }
        if (!class_exists($class)) {
            ResourceBase::output(400, '', 'uri error or resource class not fount');
        }

        $resourceInstance = new $class();
        $uriMapConfig = $resourceInstance->setUriMatchConfig();
        $matchRes = ClientRouter::mapUri($uriMatch['resource_uri'], $uriMapConfig);
        if (empty($matchRes)) {
            ResourceBase::output(400, '', 'URL error or Action not fount');
        }
        //赋值uri 中数据信息
        $resourceInstance->URI_DATA = $matchRes['uri_data'];
        ResourceBase::$ACTION_NAME = $matchRes['method_config'][$uriMatch['method']];
        //兼容赋值到clientPara
        if (!empty($matchRes['uri_data'])) {
            foreach ($matchRes['uri_data'] as $key => $value) {
                HttpUtil::$param[$key] = $value;
            }
        }
        HttpUtil::initParams();

        //通过路由指定调用方法
        $funcName = $matchRes['method_config'][$uriMatch['method']] . 'Action';
        if (!method_exists($resourceInstance, $funcName)) {
            ResourceBase::output(500, '', 'resource function not exists.');
        }

        //传入参数统一校验处理
        $checkRes = $resourceInstance->validateParam();
        if ($checkRes !== true) {       //参数处理失败，返回错误
            ResourceBase::display($checkRes);exit;
        }
        $resourceInstance->$funcName();
    }
}

$instance = new IndexBase();
$instance->dispatch();


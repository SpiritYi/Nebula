<?php
/**
 * api 接口入口文件
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/15
 * @copyright nebula.com
 */

require_once dirname(__FILE__) . '/../config/DirConfig.inc.php';
require_once CODE_BASE . '/util/logger/Logger.class.php';

require_once API . '/assembly/config/status_errcode/BaseStatusCode.config.php';
require_once API . '/assembly/ResourceBase.class.php';

class IndexBase {
    public static function dispatch() {
        //加载通用错误配置信息
        ResourceBase::$ERR_MSG = BaseStatusCode::$BASE_ERR_MSG;

        require_once API . '/assembly/ClientRouter.class.php';
        //解析url 获取相应数据
        $uriMatch = ClientRouter::match($_SERVER['REQUEST_URI']);
        if (empty($uriMatch)) {
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_NOT_FOUND, '', 'uri match failed.');
            ResourceBase::display($res);exit;
        }
        //支持的请求方式
        $allowMethod = array('GET', 'POST', 'PUT', 'DELETE');
        if (!in_array($uriMatch['method'], $allowMethod)) {
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_REQUEST_METHOD);
            ResourceBase::display($res);exit;
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
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_NOT_FOUND, '', 'route error or resource not fount');
            ResourceBase::display($res);exit;
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
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_NOT_FOUND, '', 'uri error or resource class not fount');
            ResourceBase::display($res);exit;
        }

        $resourceInstance = new $class();
        $uriMapConfig = $resourceInstance->setUriMatchConfig();
        $matchRes = ClientRouter::mapUri($uriMatch['resource_uri'], $uriMapConfig);
        if (empty($matchRes)) {
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_NOT_FOUND, '', 'URL error or Action not fount');
            ResourceBase::display($res);exit;
        }
        //赋值uri 中数据信息
        $resourceInstance->URI_DATA = $matchRes['uri_data'];
        ResourceBase::$ACTION_NAME = $matchRes['method_config'][$uriMatch['method']];
        //兼容赋值到clientPara
        if (!empty($matchRes['uri_data'])) {
            foreach ($matchRes['uri_data'] as $key => $value) {
                clientPara::$param['get'][$key] = $value;
            }
        }

        //通过路由指定调用方法
        $funcName = $matchRes['method_config'][$uriMatch['method']] . 'Action';
        if (!method_exists($resourceInstance, $funcName)) {
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_SYSTEM, '', 'resource function not exists.');
            ResourceBase::display($res);exit;
        }

        //加载当前访问类目错误配置
        // $errClass = ucfirst($uriMatch['group_root']) . 'ErrCode';
        // $errCodeConfigFile =  API . '/assembly/config/status_errcode/' . $errClass . '.config.php';
        // if (file_exists($errCodeConfigFile)) {
        //     require_once $errCodeConfigFile;
        //     $errCodeInstance = new $errClass();
        //     ResourceBase::$ERR_MSG += $errCodeInstance->err_msg;
        // }

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


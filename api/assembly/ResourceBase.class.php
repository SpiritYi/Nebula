<?php
/**
 * 资源基类
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/15
 * @copyright nebula.com
 */

abstract class ResourceBase {
    public static $ERR_MSG = array();       //存放所有可使用的错误码
    public static $ACTION_NAME = '';        //当前执行的动作类型
    protected $_param = array();                //存放所有请求参数
    public $URI_DATA = array();             //uri 中的参数，应该被废弃

    /**
     * 以固定格式返回数据，200之外的统一返回
     * @param $httpStatus int    http status code
     * @param $errMsg string    错误提示
     * @param $errDetail string 错误详细描述
     * @param $errCode int
     */
    public static function formatReturn($httpStatus, $data, $errMsg, $errCode = 0) {
        $returnArr = array(
            'http_status' => $httpStatus,
            'message' => $errMsg,
            'code' => $errCode,
            'data' => $data,
        );
        if (!in_array($httpStatus, BaseStatusCode::$HTTP_STATUS)) {
            $returnArr['http_status'] = BaseStatusCode::HTTP_400;
        }
        return $returnArr;
    }

    // public static function formatRes($errCode, $httpStatus = '', $errMsg = '', $data = '') {
    //     $httpStatus = empty($httpStatus) ? self::$ERR_MSG[$errCode]['0'] : $httpStatus;
    //     $errMsg     = empty($errMsg) ? self::$ERR_MSG[$errCode]['1'] : $errMsg;
    //     if ($errCode > 0) $errCode = 0;

    //     $returnArr = array(
    //         'http_status' => $httpStatus,
    //         'code' => $errCode,
    //         'message' => $errMsg,
    //         'data' => $data,
    //     );
    //     if (!in_array($httpStatus, BaseStatusCode::$HTTP_STATUS)) {
    //         $returnArr['http_status'] = BaseStatusCode::HTTP_400;
    //     }
    //     return $returnArr;
    // }

    /**
     * 输出需要返回数据
     * @param $data array
     * @param $disType enum(json)
     */
    public static function display($data, $disType = 'json') {
        $httpStatus = empty($data['http_status']) ? BaseStatusCode::HTTP_200 : $data['http_status'];
        unset($data['http_status']);
        header('HTTP/1.1 ' . $httpStatus);

        //低版本nginx 兼容，手动添加header 返回
        header('Content-Type:application/json');
        // if (!in_array($httpStatus, array(200, 201, 204, 206, 301, 302, 303, 304, 307))) {
        //     header('Access-Control-Allow-Credentials:true');
        //     header('Access-Control-Allow-Headers:customerId, clientAgent, GjData-Version, versionId, model, agency, contentformat, userId, token, mac, interface, X-Ganji-Agent, X-Ganji-Channel');
        //     header('Access-Control-Allow-Methods:POST, GET, OPTIONS, DELETE, PUT');
        //     $httpReferer = 'http://sta.ganji.com';
        //     if (isset($_SERVER['HTTP_ORIGIN']) && preg_match("/^http:\/\/3g.ganji.com.*/", $_SERVER['HTTP_ORIGIN'])) {
        //         $httpReferer = 'http://3g.ganji.com';
        //     }
        // }

        switch ($disType) {
            case 'json':
                echo json_encode($data);
                break;

            default:
                header('HTTP/1.0 500');
                $data = array('message' => 'error echo type.', 'detail' => 'function display() error param $disType', 'code' => -1);
                echo json_encode($data);
                break;
        }
    }

    //api 接口输出数据
    public static function output($httpCode, $data, $errMsg = '', $errCode = 0) {
        $res = self::formatReturn($httpCode, $data, $errMsg, $errCode);
        self::display($res);
        exit;
    }

    //分发参数
    public function validateParam() {
        $allConfig = $this->setParamConfig();
        if (empty($allConfig)) return true;
        if (!isset($allConfig[self::$ACTION_NAME])) {       //给了参数配置，就限定所有函数都要定义key 值
            return ResourceBase::formatRes(BaseStatusCode::ERR_SYSTEM, '', 'action:' . self::$ACTION_NAME . ' not set param config');
        }
        $paramConfig = $allConfig[self::$ACTION_NAME];

        if (is_array(current($paramConfig))) {      //如果是action_type 分类的，参数配置是两层
            $this->_param['action_type'] = clientPara::getArg('action_type');
            if (empty($this->_param['action_type']))
                return ResourceBase::formatRes(BaseStatusCode::ERR_PARAM, '', 'action_type unavailable or server config error.');
            $paramConfig = $paramConfig[$this->_param['action_type']];
        }
        foreach ($paramConfig as $paramKey => $paramType) {
            switch ($paramType) {
                case 'string':
                    $this->_param[$paramKey] = (string)clientPara::getArg($paramKey);
                    break;

                case 'int':
                    $this->_param[$paramKey] = (int)clientPara::getArg($paramKey);
                    break;

                case 'float':
                    $this->_param[$paramKey] = (float)clientPara::getArg($paramKey);
                    break;

                case 'bool':
                case 'boolean':
                    $this->_param[$paramKey] = (bool)clientPara::getArg($paramKey);
                    break;

                case 'array':
                    $this->_param[$paramKey] = json_decode(clientPara::getArg($paramKey), true);
                    break;

                default:
                    $this->_param[$paramKey] = clientPara::getArg($paramKey);
                    break;
            }
            //过参数校验
            $checkRes = $this->checkParam($paramKey);
            if (is_array($checkRes)) {
                return $checkRes;
            }
        }
        return true;
    }

    abstract function setUriMatchConfig();
    public function setParamConfig() {}
    public function checkParam() {}

}

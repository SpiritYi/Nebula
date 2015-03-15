<?php

/**
 * 解析安装id 变成数字
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2014/11/25
 * @copyright ganji.com
 */
class DeviceInfo extends ResourceBase {

    public function setUriMatchConfig() {
        return array(
            '/deviceinfo/' => array(
                'GET' => 'getInstallId',
                'POST' => 'collectDeviceInfo',
            ),
        );
    }
    
    public function setParamConfig() {
        return array(
            'getInstallId' => array(
            ),
            'collectDeviceInfo' => array(
                'user_id' => 'int', //用户id
                'device_id' => 'string', //设备id：android为regId，ios为device_token
                'city' => 'int', //城市id
            ),
        );
    }
    
    public function checkParam($paramKey) {
        switch ($paramKey) {
            case 'device_id':
                if (!in_array(strlen($this->_param[$paramKey]), array(64, 108))) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_URI_NOT_FOUND, '', 'device_id error.');
                }
                break;
        }
        return true;
    }

    public function getInstallIdAction() {
        $installId = clientPara::getArg('userId');

        //数字直接返回
        if (is_numeric($installId) && $installId > 10000) {
            $res = ResourceBase::formatReturn(200, array('install_id' => $installId), '');
            ResourceBase::display($res);exit;
        }
        require_once CODE_BASE2 . '/app/mobile_client/util/UUIDprocess.class.php';
        $numId = UUIDprocess::decryptUUID($installId);
        if (is_numeric($numId) && $numId > 10000)  {
            $res = ResourceBase::formatReturn(200, array('install_id' => $numId), '');
            ResourceBase::display($res);exit;
        } else {
            $res = ResourceBase::formatReturn(400, '', 'decrypt failed.', -1);
            ResourceBase::display($res);exit;
        }
    }

    /**
     * 收集设备信息
     * @author chenwei5 <chenwei5@ganji.com>
     * @version 2015/01/04 14:05:00
     */
    public function collectDeviceInfoAction() {
        $deviceInfo = array(
            'installID' => clientPara::getArg('installId'),
            'token' => clientPara::getArg('token'),
            'customerID' => clientPara::getArg('customerId'),
            'versionStr' => clientPara::getArg('versionId'),
            'agency' => clientPara::getArg('agency'),
            'loginID' => $this->_param['user_id'],
            'deviceID' => $this->_param['device_id'],
            'city' => $this->_param['city'],
        );

        try {
            if (!class_exists('ClientDeviceSettingNamespace')) {
                require_once CODE_BASE2 . '/app/mobile_client/ClientDeviceSettingNamespace.class.php';
            }
            $flag = ClientDeviceSettingNamespace::collectMobDevice($deviceInfo);
        } catch (Exception $exc) {
            $flag = false;
        }

        if ($flag) {
            $res = self::formatRes( CommonErrCode::ERR_SUCCESS, '', '保存设备信息成功' );
            self::display( $res ); return;
        } else {
            $res = self::formatRes( CommonErrCode::ERR_SYSTEM, '', '保存设备信息失败' );
            self::display( $res ); return;
        }
    }

}

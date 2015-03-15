<?php
/**
 * 用户token资源相关处理resource
 * @author jiajianming <jiajianmng@ganji.com>
 * @version 2014/12/20
 * @copyright ganji.com
 */
require_once CLIENT_API . '/common/user/model/UserTokenModel.class.php';
class UserToken extends ResourceBase {

    public function setUriMatchConfig() {
        return array(
            '/usertoken/' => array(
                'POST' => 'login',        //登录
            ),
            '/usertoken/:user_id/' => array(
                'DELETE' => 'loginOut',    //注销
            ),
        );
    }
    public function setParamConfig() {
        return array(
            'login' => array(
                1 => array(
                    'action_type' => 'int', //登录类型 1：表示普通用户名密码登录
                    'login_name' => 'string', //用户名称
                    'password'  => 'string', //登录密码
                    'captcha'   => 'string', //图片验证码
                ),
                2 => array(
                    'action_type' => 'int', //登录类型 2：表示手机号验证码登录
                    'phone'     => 'string', //手机号码
                    'code'   => 'string', //手机验证码
                ),
                3 => array(
                    'action_type' => 'int', //登录类型 3：第三方登录
                    'fr'           => 'string',//第三方登录来源
                    'access_token' => 'string', //第三方授权token
                    'open_id'      => 'string', //第三方openId
                    'nickname'     => 'string',//第三方昵称
                ),
                4 => array (
                    'action_type' => 'int', //登录类型 4：自动登录
                    'user_id'     => 'int',  //用户id
                    'token'        => 'string',//登录token
                ),
            ),
            'loginOut' => array(
                'user_id' => 'int',
            ),
        );
    }
    public function checkParam($paramKey) {
        switch ($paramKey) {
            case 'login_name':
            case 'user_name':
            case 'password':
            case 'code' :
            case 'access_token':
            case 'open_id':
            case 'token':
                if (empty($this->_param[$paramKey])) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_PARAM, '', $paramKey . ' unavailable.');
                }
                break;
            case 'fr':
                if (!in_array($this->_param[$paramKey],array('qq','sina')) ) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_PARAM);;
                }
                break;
            case 'user_id':
                if ($this->_param[$paramKey] <= 0) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_PARAM);;
                }
                break;
            case 'phone':
                if ( empty($this->_param[$paramKey]) || !preg_match( "/1[3458]{1}\d{9}$/", $this->_param[$paramKey]) ) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_PARAM);;
                }
                break;
        }
    }

    /**
     * POST /api/common/user/usertoken/
     */
    public function loginAction () {
        $this->privacy['useragent'] = clientPara::getArg('clientAgent');
        $this->privacy['sessionid'] = clientPara::getArg('installId');
        $this->privacy['cookie']    = clientPara::getArg('installId');
        $this->privacy['ip']        = HttpNamespace::getIp();

        switch ($this->_param['action_type']) {
            //获取用户信息
            case 1 :
                if (!empty($this->_param['captcha'])) {
                    $this->privacy['captcha'] = $this->_param['captcha'];
                }
                $userInfo = LoginNamespace::login($this->_param['login_name'], $this->_param['password'], $this->privacy);
                if (is_array($userInfo) && !empty($userInfo['code'])) {
                    if ($userInfo['code'] == 'ERR_PWD') {
                        $res = ResourceBase::formatRes(CommonErrCode::ERR_USERNAME_PWD);
                    } else if ($userInfo['code'] == 'ERR_LOCKED') {
                        $res = ResourceBase::formatRes(CommonErrCode::ERR_LOCKED);
                    } else if ($userInfo['code'] == 'NEED_CAPTCHA') {
                        $res = ResourceBase::formatRes(CommonErrCode::ERR_NEED_CAPTCHA);
                    }else if ($userInfo['code'] == 'ERR_PWD_CAPTCHA') {
                        $res = ResourceBase::formatRes(CommonErrCode::ERR_PWD_CAPTCHA);
                    } else if ($userInfo['code'] == 'ERR_CAPTCHA') {
                        $res = ResourceBase::formatRes(CommonErrCode::ERR_CAPTCHA);
                    }
                    ResourceBase::display($res);
                    return false;
                }
                break;
            case 2 :
                $userInfo = UserTokenModel::phoneLogin($this->_param['phone'], $this->_param['code'], $this->privacy);
                break;
            case 3 :
                $userInfo = UserTokenModel::thirdpartyLogin($this->_param['fr'], $this->_param['open_id']);
                if (is_array($userInfo) && empty($userInfo['nickname']) && !empty($this->_param['nickname'])){
                    $userInfo['nickname'] = $this->_param['nickname'];
                }
                break;
            case 4 :
                if(!clientPara::auth_check($this->_param['user_id'], $this->_param['token'])) {
                    $res = ResourceBase::formatRes(CommonErrCode::ERR_NEED_PERMISSION, '', '用户校验失败，请重新登录');
                    ResourceBase::display($res);
                    return false;
                }
                $ssCode   = clientPara::token2ssCode($this->_param['token']);
                $userInfo = UserTokenModel::autoLogin($ssCode, $this->privacy);
                break;
        }
        if ($userInfo === false || $userInfo['user_id'] < 0 ) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SYSTEM);
            ResourceBase::display($res);
            return false;
        }
        //获取ucsscode
        if (empty($ssCode)) {
            $ssCode = UserToolsInterface::getSscode($userInfo['user_id']);
        }
        if ($ssCode == "" || $ssCode == -1) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SYSTEM);
            ResourceBase::display($res);
            return false;
        }
        //记录登录日志
        $this->_writeLog();
        //格式化数据
        $data  = UserTokenModel::loginDataFormat($userInfo, $ssCode);
        $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS_201,'','登录成功',$data);
        ResourceBase::display($res);
        return ture;
    }

    /**
     * DELETE /api/common/user/usertoken/:id/
     */
    public function loginOutAction () {
        if(!clientPara::auth_check($this->_param['user_id'], clientPara::header('token'))) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_CHECK_LOGIN);
            ResourceBase::display($res);
            return false;
        }
        $installId = clientPara::getArg('installId');
        $data = UserTokenModel::loginOut($this->_param['user_id'],$installId);
        if (!$data) {
            //to do 记录log日志
        }
        $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS);
        ResourceBase::display($res);
        return true;
    }

    private  function _writeLog() {
        $publicParams = array(//时间,ip ,customerId，安装id，版本号，机型，渠道
            date('Y-m-d H:i:s'),
            HttpNamespace::getIp(),
            clientPara::getArg('customerId'),
            clientPara::getArg('installId'),
            clientPara::getArg('versionId'),
            clientPara::getArg('clientAgent'),
            clientPara::getArg('agency'),
        );
        if ($this->_param['action_type'] == 1) {
            $interface = 'userLogin';
            $bodyParams = array(
                $this->_param['login_name'],
                $this->_param['captcha'],
            );
        } else if($this->_param['action_type'] == 2 ) {
            $interface = 'userLogin';
            $bodyParams = array(
                $this->_param['phone'],
                $this->_param['code'],
            );
        }else if($this->_param['action_type'] == 3) {
            $interface = 'thirdPartyUserLogin';
            $bodyParams = array(
                $this->_param['fr'],
                $this->_param['open_id'],
             );
        }else if($this->_param['action_type'] == 4) {
            $interface = 'autoLogin';
            $bodyParams = array(
                $this->_param['user_id'],
                $this->_param['token'],
             );
        }
        $returnParams = array(
            $data['LoginId'],
            $ssCode
        );
        UserTokenModel::writeLoginLog($publicParams,$bodyParams,$returnParams,$interface);
        return true;
    }
}

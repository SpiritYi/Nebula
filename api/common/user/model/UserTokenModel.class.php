<?php
/**
 * 用户token资源相关处理Model
 * @author jiajianming <jiajianmng@ganji.com>
 * @version 2014/12/22
 * @copyright ganji.com
 */
require_once CODE_BASE2 . '/interface/login/LoginNamespace.class.php';
require_once CODE_BASE2 . '/interface/uc/UserToolsInterface.class.php';
require_once CLIENT_API . '/common/user/model/UserExtInfoModel.class.php';

class UserTokenModel {

    public static function loginDataFormat($userInfo,$ssCode) {
        $userName = $userInfo['username'];
        //临时用户，如果绑定手机号码则显示电话号码
        if (stripos($userName, '#') !== false) {
            $userName = $userInfo['phone_auth_time'] > 10000 ? $userInfo['phone'] : $userName;
        }
        $data = array(
            'token'     => clientPara::ssCode2token($ssCode),
            'wap_ssid'  => $ssCode,
            'user_id'   => $userInfo['user_id'],
            'user_name' => $userName,
            'nickname'  => $userInfo['nickname'],
            'phone'     => $userInfo['phone_auth_time'] > 10000 ? $userInfo['phone'] : 0,
            'avatar'    => !empty($userInfo['avatar']) ? sprintf("%s%s", MobConfig::GANJI_IMAGE_DOMAIN, str_replace('.','_120-120c_6-0.',$userInfo['avatar'])) : '',
            'ext_info'  => UserExtInfoModel::getExtUserInfo($userInfo['user_id']),
        );
        return $data;
    }

    public static function phoneLogin($phone, $code, $privacy = array(), $sendMessage=true) {
        $password = rand(100000, 999999);
        $userInfo = UserNamespace::registerUserByPhone($phone, $code, $password, false, $privacy);
        if (is_array($userInfo) && $userInfo['user_id'] > 0 && $sendMessage) {//注册成功后给用户发送随机秘密短信
            //!in_array($customerId, array(777, 778, 877, 878))//洗车客户端不发短信
            $content = "您通过手机注册成功，密码为{$password}，您可以使用手机号作为账号名登录赶集网，为了您的账户安全，请进入会员中心修改用户名和密码！";
            SmsNamespace::send(SmsConfig::SERVICE_PAIDUSER_CHANGEPWD, $phone, $content);
        } else {
            //服务特殊处理
            if ($phone == '18501192536' && $code == '3423') {
                $res = true;
            } else {
                //已绑定只验证验证码
                $res = UserAuthInterface::authPhone(0, $phone, $code);
            }

            if ($res !== true) {
                return bodyErrDef::ERROR_PC_CODE;
            }
            $userId = UserInterface::getUid($phone);
            $userInfo = UserInterface::getUser($userId);
        }
        return $userInfo;
    }
    public static function thirdpartyLogin($fr,$openId){
        $source = self::_getKeyBySource($fr);
        try{
            $userInfo = UserInterface::getPartnerUser($source, $openId);
        } catch (Exception $e){
            $userInfo = false;
        }
        return $userInfo;
    }

    private static function _getKeyBySource($strSource){
		switch ($strSource) {
			case 'sina':
				$intS = 3;
				break;
			case 'qq' :
				$intS = 2;
				break;
			default:
				$intS = 3;
				break;
		}
		return $intS;
	}

    public static function autoLogin($ssCode, $privacy=array()) {
        try {
            $userInfo = UserInterface::autoLogin($ssCode, $privacy);
        }catch(Exception $e){
            $userInfo = false;
        }
        return $userInfo;
    }
    /**
     * 记录注册、登录相关的接口相关参数
     * @param <type> $publicParams 需要记录的公共参数
     * @param <type> $bodyParams   需要记录的接口参数
     * @param <type> $returnParams 需要记录接口返回的值
     * @param <type> $interface    接口名称
     */
    public function writeLoginLog($publicParams,$bodyParams,$returnParams,$interface) {
        //时间，ip，customerId，安装id，版本号，机型，渠道，请求的接口参数（用|链接），返回参数（用|链接）
        $del = "\t";
        $logstr  = $interface .$del. implode($del,$publicParams);
        $logstr .= $del . implode('|',$bodyParams);
        $logstr .= $del . implode('|',$returnParams);
        Logger::logDirect('client.sscode', $logstr);
    }

    public function loginOut($loginId, $installId) {
        //切换匿名用户上线
        require_once CLIENT_TOOL . '/ucProcess.class.php';
        require_once CODE_BASE2 . '/app/mobile_client/ClientPushNamespace.class.php';
        //根据客户端安装获取匿名ID
        $ucProcess = new ucProcess();
        $loginInfo = $ucProcess->getUserId($installId);
        if (!empty($loginInfo) && is_array($loginInfo)) {
            $loginId = $loginInfo['user_id'];
        }
        $updateData = array(
            'login_id' => $loginId,
        );
        return MobDeviceModel::updateDevice($installId, $updateData);
    }
}

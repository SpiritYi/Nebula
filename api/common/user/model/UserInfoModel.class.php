<?php

/**
 * @brief        用户信息资源Model
 * @desc
 *
 * @author       Gaozhifeng <gaozhifeng@ganji.com>
 * @since        2014-12-26 17:13:13
 * @copyright    ganji.com
 *
 */

require_once CODE_BASE2 . '/util/text/String.class.php';
require_once CODE_BASE2 . '/interface/uc/UserInterface.class.php';
require_once CODE_BASE2 . '/interface/uc/UserAuthInterface.class.php';
require_once CODE_BASE2 . '/interface/uc/UserToolsInterface.class.php';
require_once CODE_BASE2 . '/app/user2/include/RegisterUserValidator.class.php';
require_once CODE_BASE2 . '/app/mobile_client/ClientDeviceSettingNamespace.class.php';
require_once CLIENT_API . '/assembly/util/AppDataBaseHelper.class.php';
require_once CLIENT_API . '/common/default/model/VerificationModel.class.php';
require_once CLIENT_API . '/common/user/model/UserExtInfoModel.class.php';
require_once CLIENT_API . '/common/user/model/UserTokenModel.class.php';


class UserInfoModel {

    /**
     * 手机号注册
     *
     * @param  int    $phone    手机号码
     * @param  int    $code     手机验证码
     * @param  string $password 密码
     * @param  arry   $privacy  私有信息
     * @return succee array
     *         fail   exception
     */
    public static function phoneRegister( $phone, $code, $password, $privacy ) {
        if ( empty($phone) || empty($code) || empty($password) || empty($privacy) ) {
            $args = var_export( func_get_args(), true );
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param') );
        }

        $userInfo = UserNamespace::registerUserByPhone( $phone, $code, $password, true, $privacy );
        if ( $userInfo === false ) {
            $args = var_export( func_get_args(), true );
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error UserNamespace::registerUserByPhone') );
        }
        return $userInfo;
    }

    /**
     * 用户名注册
     *
     * @param  string $loginName 用户名
     * @param  string $password  密码
     * @param  array  $privacy   私有信息
     * @return succee array
     *         fail   exception
     */
    public static function nameRegister( $loginName, $password, $privacy ) {
        if ( empty($loginName) || empty($password) || empty($privacy) ) {
            $args = var_export( func_get_args(), true );
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param') );
        }

        try {
            $data = array(
                'user_name' => $loginName,
                'password'  => $password,
            );
            $userInfo = UserInterface::signUp( $data, $privacy );
            $userInfo['username'] = $data['user_name'];
            ///传递的password为发帖的管理密码
            return $userInfo;
        } catch ( Exception $e ) {
            $args = var_export( func_get_args(), true );
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, $e) );
        }
    }

    /**
     * 保存用户注册粒度
     * 记录安装Id缓存，Ip缓存
     *
     * @param  int $installId 安装Id
     * @return bool
     */
    public static function saveRegister2MC( $installId ) {
        if ( empty($installId) ) {
            return false;
        }
        $registerKey  = 'register_%s';
        $registerLife = 1800;

        $mcHandle = AppDataBaseHelper::getMcHandler();
        if ( $mcHandle ) {
            //安装id为key
            $mcKey = sprintf( $registerKey, $installId );
            $mcHandle->write( $mcKey, 1, $registerLife );
            //ip为key
            $ip    = HttpNamespace::getIp();
            $mcKey = sprintf( $registerKey, $ip );
            $value = $mcHandle->read( $mcKey );
            if ( $value ) {
                $mcHandle->increment( $mcKey );
            } else {
                $mcHandle->write( $mcKey, 1, $registerLife );
            }
        }
        return true;
    }

    /**
     * 验证用户注册是否超过粒度限度
     * 如果30分钟内installId注册过用户
     * 如果30分钟内该Ip注册超过10次
     *
     * @param  int  $installId 安装Id
     * @return bool true  超过
     *              false 正常
     */
    public static function checkGrainSize( $installId ) {
        if ( empty($installId) ) {
            return false;
        }
        $registerKey   = 'register_%s';
        $regieterIpNum = 10;

        $mcHandle = AppDataBaseHelper::getMcHandler();
        if ( $mcHandle ) {
            //安装id为key
            $mcKey = sprintf( $registerKey, $installId );
            $value = $mcHandle->read( $mcKey );
            if ( $value ) {
                return true;
            }
            //ip为key
            $ip    = HttpNamespace::getIp();
            $mcKey = sprintf( $registerKey, $ip );
            $value = $mcHandle->read( $mcKey );
            if ( $value && $value > $regieterIpNum ) {
                return true;
            }
        }
        return false;
    }

    /**
     * 记录登陆日志
     * @param  array  $bodyParams   body参数
     * @param  array  $returnParams 返回参数
     * @param  string $interface    接口名
     * @return bool
     */
    public static function writeloginLog( $bodyParams, $returnParams, $interface ) {
        if ( empty($bodyParams) || empty($returnParams) || empty($interface) ) {
            return false;
        }

        //时间，ip，customerId，安装id，版本号，机型，渠道
        $publicParams = array(
            date( 'Y-m-d H:i:s' ),
            HttpNamespace::getIp(),
            clientPara::header( 'customerId' ),
            clientPara::header( 'installId' ),
            clientPara::header( 'versionId' ),
            clientPara::header( 'clientAgent' ),
            clientPara::header( 'agency' ),
        );

        UserTokenModel::writeLoginLog( $publicParams, $bodyParams, $returnParams, $interface );
        return true;
    }

    /**
     * 获取用户支付地址
     * @param  int    $userId 用户Id
     * @return string $payUrl 支付地址
     */
    public static function getPayUrlByUserId( $userId ) {
        if ( empty($userId) ) {
            return false;
        }
        $wapPayUri = 'http://pay.ganji.cn';
        $channelId = 14;
        $ssId      = self::_getCodeByUserId( $userId );
        $platform  = clientPara::getPlatform();
        $versionId = clientPara::header( 'versionId' );

        $payUrl  = $wapPayUri . '?';
        $payUrl .= 'channel_id=' . $channelId;
        $payUrl .= '&session_id=' . $ssId;
        $payUrl .= '&ac=r';
        $payUrl .= '&r_url=' . $wapPayUri . '?ac=result&plat=' . $platform;
        $payUrl .= '&from_type=client' . '_' . $platform . '_' . $versionId;
        return $payUrl;
    }

    /**
     * 通过UserId得到加密值
     * @param string $userId
     */
    private static function _getCodeByUserId( $userId ) {
        $des = new MobDES( 'IfRFPrr+', 'E5PaC7Iw' );
        return $des->encrypt( $userId . '|' . time() );
    }

    /**
     * 格式化用户头像
     * 格式:group_name/Mxx/xx/xx/file_id，其中group_name以gjfs开头且长度不超过8位，file_id为34位或50位
     *
     * @param  string $avatar 头像地址
     * @return string 去掉尺寸参数
     */
    public static function formatUserAvatarUrl( $avatar ) {
        $args = var_export( func_get_args(), true );
        if ( empty($avatar) ) {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param') );
        }

        //返回地址被格式化，还原原始地址
        $pathParam  = explode( '.', $avatar );
        $pathParam2 = explode( '_', current($pathParam) );
        $prefix  = current( $pathParam2 );
        $postfix = next( $pathParam );
        if ( empty($prefix) || empty($postfix)) {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param Value') );
        }

        return $prefix . '.' . $postfix;
    }

    /**
     * 重置密码（目前仅支持手机找回）
     *
     * @param  string $method      找回方式
     * @param  string $methodKey   对应方式的值（手机号）
     * @param  string $newPassword 新密码
     * @return array/int/exception -2   用户要修改的密码和用户在赶集网其他密码相同;
     *                             正常 用户重置后的用户信息
     *                             异常 捕获并记录日志
     */
    public static function resetPassword( $installId, $method, $methodKey, $newPassword ) {
        $args = var_export( func_get_args(), true );
        if ( empty($method) || empty($methodKey) || empty($newPassword) ) {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param') );
        }

        try {
            $mcHandle = AppDataBaseHelper::getMcHandler();
            if ( $mcHandle ) {
                $mcKey   = VerificationModel::getCaptchaMcKey( 3, $installId , $method );
                $mcValue = $mcHandle->read( $mcKey );
                if ( !empty($mcValue) && is_array($mcValue) && $mcValue['phone'] == $methodKey ) {
                    $userId = UserInterface::getUid( $methodKey );
                    if ( $userId <= 0 ) {
                        throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error UserInterface::getUid') );
                    }
                    $result = UserAuthInterface::setPassword( $userId, $newPassword, '通过手机找回密码' );
                    if ( $result == -2 ) {
                        return $result;
                    } else {
                        $mcHandle->delete( $mcKey );
                        $userInfo = UserInterface::getUser( $userId );
                        $userInfo['sscode'] = $result;
                        return $userInfo;
                    }
                } else {
                    throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param Value') );
                }
            } else {
                throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error AppDataBaseHelper::getMcHandler') );
            }
        } catch ( Exception $e ) {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, $e) );
        }
    }

}

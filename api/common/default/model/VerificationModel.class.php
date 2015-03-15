<?php

/**
 * @brief        验证资源Model
 * @desc
 *
 * @author       Gaozhifeng <gaozhifeng@ganji.com>
 * @since        2015-1-4 16:07:37
 * @copyright    ganji.com
 *
 */

require_once CODE_BASE2 . '/util/log/Logger.class.php';
require_once CODE_BASE2 . '/util/check_code/CheckCode.class.php';
require_once CODE_BASE2 . '/interface/uc/UserInterface.class.php';
require_once CODE_BASE2 . '/interface/uc/UserAuthInterface.class.php';
require_once CODE_BASE2 . '/interface/login/LoginNamespace.class.php';
require_once CODE_BASE2 . '/app/user2/UserPhoneNamespace.class.php';
require_once CODE_BASE2 . '/app/mobile_client/util/UUIDprocess.class.php';
require_once CLIENT_API . '/assembly/util/AppDataBaseHelper.class.php';
require_once CLIENT_API . '/common/user/model/UserInfoModel.class.php';


class VerificationModel {

    /**
     * 设置sessionId
     * @param  int $installId  安装Id
     * @return
     */
    public static function setSessionId( $installId ) {
        $args = var_export( func_get_args(), true );
        if ( empty($installId) ) {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param') );
        }

        session_id( $installId );
    }

    /**
     * 保存验证码到mc
     * @param  int    $installId  key前缀
     * @param  int    $custmoerId key后缀
     * @param  string $value      验证码值
     * @return succee bool
     *         fail   exception
     */
    public static function saveCaptcha2Mc( $installId, $custmoerId, $value ) {
        $args = var_export( func_get_args(), true );
        if ( empty($installId) || empty($custmoerId) || empty($value) ) {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param') );
        }

        $mcKey    = self::getCaptchaMcKey( 1, $installId, $custmoerId );
        $mcHandle = AppDataBaseHelper::getMcHandler();
        if ( $mcHandle ) {
            $mcHandle->write( $mcKey, $value, 600 );
        } else {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error AppDataBaseHelper::getMcHandler') );
        }

        return true;
    }

    /**
     * 验证图片验证码
     * type为 1.统一验证，2.找回密码 typeKey为手机号码
     * 常规格式：安装Id_checkcode_客户端Id
     * 找回密码：安装Id_checkcode_phone
     *
     * @param  string $captcha 验证码
     * @param  int    $prefix  key前缀
     * @param  int    $postfix key后缀
     * @param  int    $type    用途
     * @param  string $typeKey 对应值
     * @return succee bool true
     *         fail   exception
     */
    public static function checkCaptcha( $captcha, $prefix, $postfix, $type = 1, $typeKey = '' ) {
        $args = var_export( func_get_args(), true );
        if ( empty($captcha) || empty($prefix) || empty($postfix) || ($type == 2 && $typeKey == '') ) {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param') );
        }

        $mcHandle = AppDataBaseHelper::getMcHandler();
        if ( $mcHandle ) {
            $mcKey = self::getCaptchaMcKey( 1, $prefix, $postfix );
            $mcValue = $mcHandle->read( $mcKey );
            if ( strcasecmp($mcValue, $captcha) == 0 ) {
                $mcHandle->delete( $mcKey );
                //找回密码，记录步骤mc
                if ( $type == 2 ) {
                    $mcNewKey = self::getCaptchaMcKey( 2, $prefix, 'phone' );
                    $mcHandle->write( $mcNewKey, $typeKey, 600 );
                }
                return true;
            } else {
                throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param Value') );
            }
        } else {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error AppDataBaseHelper::getMcHandler') );
        }
    }

    /**
     * 获取验证码Mc缓存Key
     * @param  int    $method  方式
     * @param  string $prefix  前缀
     * @param  string $postfix 后最
     * @return string
     */
    public static function getCaptchaMcKey( $method, $prefix, $postfix ) {
        $mcKeys = array(
            1 => '%s_checkcode_%s',
            2 => '%s_getpassword2_%s',
            3 => '%s_getpassword3_%s',
        );

        return sprintf( $mcKeys[$method], $prefix, $postfix );
    }

    /**
     * 验证找回密码手机验证部分
     * @return succee bool true
     *         fail   exception
     */
    public static function setGetBackPasswordStep( $installId, $phone, $code ) {
        $args = var_export( func_get_args(), true );
        if ( empty($installId) || empty($phone) || empty($code) ) {
            throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error Param') );
        }

        $mcHandle = AppDataBaseHelper::getMcHandler();
        if ( $mcHandle ) {
            $mcKey   = self::getCaptchaMcKey( 2, $installId, 'phone' );
            $mcValue = $mcHandle->read( $mcKey );
            if ( !empty($mcValue) && $mcValue == $phone ) {
                $mcNewKey = self::getCaptchaMcKey( 3, $installId, 'phone' );
                $mcNewValue = array(
                    'method' => 'phone',
                    'phone'  => $phone,
                    'code'   => $code,
                );
                $mcHandle->write( $mcNewKey, $mcNewValue, 600 );
                return true;
            } else {
                throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error No found step one') );
            }
        } else {
             throw new Exception( sprintf('func:[%s] args:[%s] emsg:[%s]', __FUNCTION__, $args, 'Error AppDataBaseHelper::getMcHandler') );
        }
    }

}

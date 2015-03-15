<?php

/**
 * @brief        验证资源
 * @desc
 *
 * @author       Gaozhifeng <gaozhifeng@ganji.com>
 * @since        2015-1-4 16:07:37
 * @copyright    ganji.com
 *
 */

require_once CLIENT_API . '/common/default/model/VerificationModel.class.php';


class Verification extends ResourceBase {

    /**
     * 设置uri方法映射
     * @return array
     */
    public function setUriMatchConfig() {
        return array(
            '/verification/captcha/' => array(
                'GET'  => 'getCaptcha',
                'POST' => 'checkCaptcha',
            ),
            '/verification/code/' => array(
                'GET'  => 'getCode',
                'POST' => 'checkCode',
            ),
        );
    }

    /**
     * 参数配置
     * @var array
     */
    public function setParamConfig() {
        return array(
            'getCaptcha' => array(
                'type'   => 'int',         //用途
                'width'  => 'int',         //宽度
                'height' => 'int',         //高度
            ),
            'checkCaptcha' => array(
                'captcha'  => 'string',    //图片验证码
                'type'     => 'int',       //用途
                'type_key' => 'string',    //找回实体，目前仅为手机号码
            ),
            'getCode' => array(
                'phone'    => 'int',       //手机号码
                'type'     => 'int',       //用途
                'user_id'  => 'int',       //用户Id
            ),
            'checkCode' => array(
                'phone'    => 'int',       //手机号码
                'type'     => 'int',       //用途
                'user_id'  => 'int',       //用户Id
                'code'     => 'int',       //手机验证码
            ),
        );
    }

    /**
     * 参数验证
     * @param  string $name  检测参数
     * @return array  $param 参数数组
     */
    public function checkParam( $name ) {

        //集中验证
        switch ( $name ) {
            case 'captcha':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '验证码不能为空', -2 );
                }
                break;

            case 'phone':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '手机号码不能为空', -2 );
                }
                if ( !preg_match( '/1[3458]{1}\d{9}$/', $this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '怪怪的手机号码，请重新输入', -2 );
                }
                break;

            case 'type':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '类型不能为空', -2 );
                }
                break;

            case 'user_id':
                if ( $action == 'getCode' && $this->_param['type'] == 4 && empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '用户Id不能为空', -2 );
                }
                break;

            case 'code':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '手机验证码不能为空', -2 );
                }
                break;

            case 'width':
                if ( empty($this->_param[$name]) ) {
                    $this->_param[$name] = 120;
                }
                break;

            case 'height':
                if ( empty($this->_param[$name]) ) {
                    $this->_param[$name] = 35;
                }
                break;

            default:
                break;
        }

        return true;
    }

    /**
     * 获取图片验证码
     * @return
     */
    public function getCaptchaAction() {

        try {
            $options = array(
                'width'      => $this->_param['width'],
                'height'     => $this->_param['height'],
                'background' => CODE_BASE2 . '/util/check_code/captcha_new/backgrounds/bg3.jpg',
            );

            $installId  = clientPara::header( 'installId' );
            $customerId = clientPara::header( 'customerId' );

            //统一获取
            if ( $this->_param['type'] == 1 ) {
                //获取验证码值并输出
                $code = CheckCode::complex_new_default( $options, $customerId );
                //将验证码值存入mc
                VerificationModel::saveCaptcha2Mc( $installId, $customerId, $code );
            } else if ( $this->_param['type'] == 2 ) {
                //登陆验证码用session进行验证
                VerificationModel::setSessionId( $installId );
                //指定验证码值并输出
                $code = LoginNamespace::getCaptcha( $installId );
                CheckCode::complex_new_default( $options, $customerId, $code );
            }
            ///登陆验证码
        } catch ( Exception $e ) {
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'Verification.getCaptcha' );
        }
    }

    /**
     * 验证图片验证码
     * @return array
     */
    public function checkCaptchaAction() {

        try {
            //构成验证前后缀
            $installId  = clientPara::header( 'installId' );
            $customerId = clientPara::header( 'customerId' );

            //验证验证码
            VerificationModel::checkCaptcha( $this->_param['captcha'], $installId, $customerId, $this->_param['type'], $this->_param['type_key'] );
            $res = self::formatRes( CommonErrCode::ERR_SUCCESS );
        } catch ( Exception $e ) {
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'Verification.checkCaptcha' );
            $res = self::formatRes( CommonErrCode::ERR_CAPTCHA );
        }

        self::display( $res ); return;
    }

    /**
     * 获取手机验证码
     * @return array
     */
    public function getCodeAction() {

        try {
            //用途是绑定手机，判断该手机号码是否已经绑定，没有绑定，则下发
            //用途是找回密码，确认该手机号码是否绑定账号，已有绑定，则下发
            if ( ($this->_param['type'] == 2 || $this->_param['type'] == 4) && UserInterface::getUid($this->_param['phone']) >= 0 ) {
                $res = self::formatReturn( 400, '', '该手机已绑定账号', -2 );
                self::display( $res ); return;
            } else if ( $this->_param['type'] == 3 && UserInterface::getUid($this->_param['phone']) < 0 ) {
                $res = self::formatReturn( 400, '', '该手机未绑定账号', -2 );
                self::display( $res ); return;
            }

            //发送手机验证码
            $flag = UserPhoneNamespace::sendAuthCodeByPhone( $this->_param['phone'], $this->_param['user_id'] );
            if ( $flag == 1 ) {
                $res = self::formatReturn( 200, '', '成功', 0 );
            } else if ( $flag == -1 ) {
                $res = self::formatReturn( 400, '', '验证码获取达到上限，请24小时后重试', -2 );
            } else if ( $flag == -2 ) {
                $res = self::formatReturn( 400, '', '60秒内已接收过验证码，请稍后再试', -2 );
            } else {
                $res = self::formatReturn( 400, '', '发送验证码失败，请重试', -2 );
            }
        } catch ( Exception $e ) {
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'Verification.getCode' );
            $res = self::formatReturn( 500, '', '发送验证码失败，请重试', -2 );
        }

        self::display( $res ); return;
    }

    /**
     * 验证手机验证码
     * @return array
     */
    public function checkCodeAction() {

        try {
            $flag = UserAuthInterface::authPhone( $this->_param['user_id'], $this->_param['phone'], $this->_param['code'] );
            if ( $flag === true ) {
                //找回密码特殊处理
                if ( $this->_param['type'] == 3 ) {
                    $installId = clientPara::header( 'installId' );
                    VerificationModel::setGetBackPasswordStep( $installId, $this->_param['phone'], $this->_param['code'] );
                }
                $res = self::formatRes( CommonErrCode::ERR_SUCCESS );
            } else if ( $flag == -1 ) {
                $res = self::formatReturn( 400, '', '参数错误', -2 );
            } else if ( $flag == -2 ) {
                $res = self::formatReturn( 400, '', '尝试次数过多', -2 );
            } else if ( $flag == -3 ) {
                $res = self::formatReturn( 400, '', '手机已经绑定在其他账户上', -2 );
            } else {
                $res = self::formatReturn( 400, '', '手机验证码错误', -2 );
            }
        } catch ( Exception $e ) {
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'Verification.checkCode' );
            $res = self::formatReturn( 400, '', '手机验证码验证失败', -2 );
        }

        self::display( $res ); return;
    }

}

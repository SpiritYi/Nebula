<?php

/**
 * @brief        用户信息资源
 * @desc
 *
 * @author       Gaozhifeng <gaozhifeng@ganji.com>
 * @since        2014-12-20 18:08:27
 * @copyright    ganji.com
 *
 */

require_once CLIENT_API . '/common/user/model/UserInfoModel.class.php';


class UserInfo extends ResourceBase {

    /**
     * 设置uri方法映射
     * @return array
     */
    public function setUriMatchConfig() {
        return array(
            '/userinfo/' => array(
                'POST' => 'registerUser',
            ),
            '/userinfo/password/' => array(
                'PUT' => 'getBackPassword',
            ),
            '/userinfo/:id/' => array(
                'GET' => 'getUser',
            ),
            '/userinfo/:id/phone/' => array(
                'PUT' => 'authPhone',
            ),
            '/userinfo/:id/avatar/' => array(
                'PUT' => 'editUserAvatar',
            ),
            '/userinfo/:id/nickname/' => array(
                'PUT' => 'editUserNickname',
            ),
        );
    }

    /**
     * 参数配置
     * @var array
     */
    public function setParamConfig() {
        return array(
            'registerUser' => array(
                //手机号注册
                '1' => array(
                    'action_type' => 'int',       //操作类型
                    'phone'       => 'int',       //电话
                    'password'    => 'string',    //密码
                    'code'        => 'string',    //手机验证码
                ),
                //用户名注册
                '2' => array(
                    'action_type' => 'int',       //操作类型
                    'login_name'  => 'string',    //用户名
                    'password'    => 'string',    //密码
                    'email'       => 'string',    //邮箱
                    'captcha'     => 'string',    //图片验证码
                ),
            ),
            'getBackPassword' => array(
                'method_key'   => 'string',       //找回方式实体
                'new_password' => 'string',       //新密码
            ),
            'getUser' => array(
                'id' => 'int',                    //用户Id
            ),
            'authPhone' => array(
                'id'    => 'int',                 //用户Id
                'phone' => 'int',                 //手机号码
                'code'  => 'int',                 //手机验证码
            ),
            'editUserAvatar' => array(
                'id'         => 'int',            //用户Id
                'new_avatar' => 'string',         //新头像
            ),
            'editUserNickname' => array(
                'id'           => 'int',          //用户Id
                'new_nickname' => 'string',       //新昵称
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
            case 'login_name':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '登录名不能为空', -2 );
                }
                $rs = RegisterUserValidator::validatorUserData( array('user_name' => $this->_param[$name]) );
                if ( $rs !== true ) {
                    return self::formatReturn( 400, '', $rs, -2 );
                }
                break;

            case 'password':
            case 'new_password':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '登陆密码不能为空', -2 );
                }
                $rs = RegisterUserValidator::validatorUserData( array('password' => $this->_param[$name]) );
                if ( $rs !== true ) {
                    return self::formatReturn( 400, '', $rs, -2 );
                }
                break;

            case 'captcha':
                $installId  = clientPara::header( 'installId' );
                $customerId = clientPara::header( 'customerId' );
                if ( empty($this->_param[$name]) ) {
                    //检验粒度控制
                    if ( UserInfoModel::checkGrainSize( $installId ) ) {
                        return ResourceBase::formatRes( CommonErrCode::ERR_NEED_CAPTCHA );
                    }
                } else {
                    try {
                        VerificationModel::checkCaptcha( $this->_param[$name], $installId, $customerId );
                    } catch ( Exception $e ) {
                        //#return ResourceBase::formatRes( CommonErrCode::ERR_CAPTCHA );
                    }
                }
                break;

            case 'phone':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '手机号码不能为空', -2 );
                }
                if ( !preg_match('/1[3458]{1}\d{9}$/', $this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '怪怪的手机号码，请重新输入', -2 );
                }
                if ( UserInterface::getUid($this->_param['phone']) >= 0 )  {
                    return self::formatReturn( 400, '', '该手机已绑定过账号', -2 );
                }
                break;

            case 'code':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '手机验证码不能为空', -2 );
                }
                break;

            case 'email':
                if ( !empty($this->_param[$name]) ) {
                    if ( !preg_match('/^\w+((-|\.)\w+)*@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/', $this->_param[$name]) ) {
                        return self::formatReturn( 400, '', '怪怪的邮箱规则，请重新输入', -2 );
                    }
                }
                break;

            case 'new_avatar':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '头像不能为空', -2 );
                }
                //如果是带参数的图片url则进行格式化
                try {
                    $this->_param[$name] = UserInfoModel::formatUserAvatarUrl( $this->_param[$name] );
                } catch ( Exception $e ) {
                    return self::formatReturn( 400, '', '头像规则错误', -2 );
                }
                break;

            case 'new_nickname':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '昵称不能为空', -2 );
                }
                if ( String::getStringLength($this->_param[$name]) > 20 ) {
                    return self::formatReturn( 400, '', '昵称只能是10个汉字或20个字符', -2 );
                }
                break;

            case 'method_key':
                if ( empty($this->_param[$name]) ) {
                    return self::formatReturn( 400, '', '手机号码不能为空', -2 );
                }
                break;

            default:
                break;
        }

        return true;
    }

    /**
     * 用户注册
     * @return array
     */
    public function registerUserAction() {

        try {
            //私有信息
            $privacy = array(
                'ip'        => HttpNamespace::getIp(),
                'cookie'    => clientPara::header( 'installId' ),
                'useragent' => clientPara::header( 'clientAgent' ),
            );
            //手机号注册
            if ( $this->_param['action_type'] == 1 ) {
                $userInfo = UserInfoModel::phoneRegister( $this->_param['phone'], $this->_param['code'], $this->_param['password'], $privacy );
            } else {
                $privacy['email'] = $this->_param['email'];
                $userInfo = UserInfoModel::nameRegister( $this->_param['login_name'], $this->_param['password'], $privacy );
            }
            ///用户名注册

            //获取用户登录后客户端依赖数据
            $ssCode = $userInfo['password'];
            $data   = UserTokenModel::loginDataFormat( $userInfo, $ssCode );

            //保存力度控制值
            UserInfoModel::saveRegister2MC( $privacy['cookie'] );

            //记录登陆日志
            if ( $this->_param['action_type'] == 1 ) {
                $bodyParams = array(
                    $this->_param['phone'],
                    $this->_param['code'],
                );
            } else {
                $bodyParams = array(
                    $this->_param['login_name'],
                );
            }
            $returnParams = array(
                $data['user_id'],
                $ssCode,
            );
            UserInfoModel::writeloginLog( $bodyParams, $returnParams, 'register' );

            $res = self::formatRes( CommonErrCode::ERR_SUCCESS, '', '', $data );

        } catch ( Exception $e ) {
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'UserInfo.registerUser' );
            $res = self::formatReturn( 500, '', '创建用户失败', -2 );
        }

        self::display( $res ); return;
    }

    /**
     * 找回密码
     * @return array
     */
    public function getBackPasswordAction() {

        try {
            $installId  = clientPara::header( 'installId' );
            //目前仅支持手机找回
            $result = UserInfoModel::resetPassword( $installId, 'phone', $this->_param['method_key'], $this->_param['new_password'] );
            if ( $result == '-2' ) {
                $res = self::formatReturn( 400, '', '登录密码与支付密码不能相同，请重新设置', -2 );
            } else {
                //获取用户登录后客户端依赖数据
                $data = UserTokenModel::loginDataFormat( $result, $result['sscode'] );

                //记录登陆日志
                $bodyParams = array(
                    $this->_param['method_key'],
                );
                $returnParams = array(
                    $data['user_id'],
                    $result['sscode'],
                );
                UserInfoModel::writeloginLog( $bodyParams, $returnParams, 'GetBackPassword' );

                $res = self::formatRes( CommonErrCode::ERR_SUCCESS, '', '', $data );
            }
            ///返回登录信息
        } catch ( Exception $e ) {
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'UserInfo.getBackPassword' );
            $res = self::formatReturn( 500, '', '重置密码失败', -2 );
        }

        self::display( $res ); return;
    }

    /**
     * 获取用户
     * @return array 用户信息
     */
    public function getUserAction() {

        //用户登录校验
        if ( !clientPara::auth_check($this->_param['id'], clientPara::header( 'token' )) ) {
            $res = self::formatRes( CommonErrCode::ERR_CHECK_LOGIN );
            self::display( $res ); return;
        }

        //获取用户
        try {
            $userInfo = UserInterface::getUser( $this->_param['id'] );

            //用户扩展
            $userExtInfo = UserExtInfoModel::getExtUserInfo( $userInfo['user_id'] );
            //用户头像
            $avatar      = !empty( $userInfo['avatar'] ) ? sprintf( '%s%s', MobConfig::GANJI_IMAGE_DOMAIN, str_replace('.','_120-120c_6-0.', $userInfo['avatar']) ) : '';
            //用户手机
            $userPhone   = $userInfo['phone_auth_time'] > 10000 ? $userPhone = $userInfo['phone'] : 0;
            //获取支付Url
            $payUrl      = UserInfoModel::getPayUrlByUserId( $userInfo['user_id'] );

            //构成返回数组
            $data = array(
                'user_id'           => $userInfo['user_id'],
                'user_name'         => $userInfo['user_name'],
                'nickname'          => $userInfo['nickname'],
                'avatar'            => $avatar,
                'phone'             => $userPhone,
                'pay_url'           => $payUrl,
                'account'           => $userExtInfo['account'],              //用户账户余额
                'post_num'          => $userExtInfo['post_num'],             //我发布的帖子数
                'fav_num'           => $userExtInfo['fav_num'],              //我的收藏数
                'remain_resume_num' => $userExtInfo['remain_resume_num'],    //获得用户剩余下载简历点数
                'biz_info'          => $userExtInfo['biz_info'],             //获取商业属性
                'im_id'             => $userExtInfo['im_id'],                //获取用户imId（群聊Id）
            );
            //获取用户未读的面试邀请数
            if ( in_array(clientPara::header('customerId'), array(860, 730)) ) {
                $data['interviewUnreadCount'] = $userExtInfo['interviewUnreadCount'];
            }

            $res = self::formatRes( CommonErrCode::ERR_SUCCESS, '', '', $data );
        } catch ( Exception $e ) {
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'UserInfo.getUser' );
            $res = self::formatReturn( 400, '', '用户验证失败，请重新登录', -2 );
        }

        self::display( $res ); return;
    }

    /**
     * 手机绑定
     * @return array
     */
    public function authPhoneAction() {

        try {
            $flag = UserAuthInterface::authPhone( $this->_param['id'], $this->_param['phone'], $this->_param['code'] );
            if ( $flag === true ) {
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
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'UserInfo.authPhone' );
            $res = self::formatReturn( 400, '', '手机验证失败', -2 );
        }

        self::display( $res ); return;
    }

    /**
     * 编辑用户头像
     * @return array
     */
    public function editUserAvatarAction() {

        //用户登录校验
        if ( !clientPara::auth_check($this->_param['id'], $this->_param['token']) ) {
            $res = self::formatRes( CommonErrCode::ERR_CHECK_LOGIN );
            self::display( $res ); return;
        }

        try {
            $data = array(
                'user_id' => $this->_param['id'],
                'avatar'  => $this->_param['new_avatar'],
            );
            UserInterface::updateUser($data);
            $res = self::formatRes( CommonErrCode::ERR_SUCCESS );
        } catch ( Exception $e ) {
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'UserInfo.editUserAvatar' );
            $res = self::formatRes( CommonErrCode::ERR_SYSTEM );
        }

        self::display( $res ); return;
    }

     /**
     * 编辑用户昵称
     * @return array
     */
    public function editUserNicknameAction() {

        //用户登录校验
        if ( !clientPara::auth_check($this->_param['id'], $this->_param['token']) ) {
            $res = self::formatRes( CommonErrCode::ERR_CHECK_LOGIN );
            self::display( $res ); return;
        }

        try {
            $data = array(
                'user_id'  => $this->_param['id'],
                'nickname' => $this->_param['new_nickname'],
            );
            UserInterface::updateUser($data);
            $res = self::formatRes( CommonErrCode::ERR_SUCCESS );
        } catch ( Exception $e ) {
            Logger::logError( sprintf('body:[%s] trace:[%s],', $e->getMessage(), $e->getTraceAsString()), 'UserInfo.editUserNickname' );
            $res = self::formatRes( CommonErrCode::ERR_SYSTEM );
        }

        self::display( $res ); return;
    }

}

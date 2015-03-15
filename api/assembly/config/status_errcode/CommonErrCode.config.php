<?php
/**
 * 公共接口特殊错误code定义
 * @author jiajianming <jiajianming@ganji.com>
 * @version 2014/12/23
 * @copyright ganji.com
*/
class CommonErrCode extends BaseStatusCode {

    //公共-1000 到 -1999
    const ERR_USERNAME_PWD  = -1000; //用户名或者密码错误
    const ERR_LOCKED        = -1001; //用户被锁定
    const ERR_PWD_CAPTCHA   = -1002; //用户名或者密码错误，且需要验证码
    const ERR_REG_AUTOLOGIN = -1003; //注册成功，请稍后登录
    const ERR_GROUP_OWNER   = -1004; //用户非群主

    //积分商城
    const ERR_BUY_STRICT    = -1010;    //购买受限

    //错误码对应的http_status与错误提示信息
    public $err_msg = array(
        self::ERR_USERNAME_PWD  => array(self::HTTP_400, '用户名或密码错误'),
        self::ERR_LOCKED        => array(self::HTTP_500, '账号已锁定,有问题请联系客服'),
        self::ERR_PWD_CAPTCHA   => array(self::HTTP_400, '用户名或密码错误'),
        self::ERR_REG_AUTOLOGIN => array(self::HTTP_200, '注册成功，请登录'),
        self::ERR_GROUP_OWNER   => array(self::HTTP_400, '用户非群主'),
        self::ERR_BUY_STRICT    => array(self::HTTP_400, '购买受限'),
    );
}

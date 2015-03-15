<?php
/**
 * 所有接口http状态码以及通用错误提示code码定义
 * @author jiajianming <jiajianming@ganji.com>
 * @version 2014/12/23
 * @copyright ganji.com
*/

class BaseStatusCode {
    //http协议层数据
    const HTTP_200 = '200';     //200，ok
    const HTTP_201 = '201';     //201，创建成功
    const HTTP_202 = '202';     //202， Accepted，服务器已接受请求，但尚未处理
    const HTTP_204 = '204';     //204, NO CONTENT, 服务器成功处理，但不需要返回内容
    const HTTP_304 = '304';     //304, NOT MODIFIED, GET的数据没有更改，客户端不需要处理
    const HTTP_400 = '400';     //400, Bad Request, 请求错误
    const HTTP_404 = '404';     //404, 不存在
    const HTTP_405 = '405';     //405, Method Not Allowed
    const HTTP_500 = '500';     //500, Internal Server Error, 服务器遇到了一个未曾预料的状况，导致了它无法完成对请求的处理。

    //可允许使用的http 状态
    public static $HTTP_STATUS = array(
        self::HTTP_200, self::HTTP_201, self::HTTP_202, self::HTTP_204,
        self::HTTP_304,
        self::HTTP_400, self::HTTP_404, self::HTTP_405,
        self::HTTP_500,
    );

    //通用api错误码定义
    const ERR_SUCCESS         = 0;//无错误
    const ERR_SUCCESS_201     = 1;//无错误
    const ERR_SUCCESS_202     = 2;//无错误
    const ERR_SUCCESS_204     = 3;//无错误

    const ERR_URI_NOT_FOUND   = -1; //资源不存在
    const ERR_NOT_FOUND       = -1;
    const ERR_PARAM           = -2; //一般参数校验错误
    const ERR_CHECK_LOGIN     = -3; //需要登录或者用户校验失败
    const ERR_NEED_CAPTCHA    = -4; //需要验证码
    const ERR_CAPTCHA         = -5; //验证码错误

    const ERR_SYSTEM          = -999; //系统错误
    const ERR_REQUEST_METHOD  = -998; //请求方式不支持

    //错误码对应的http_status与错误提示信息
    public static $BASE_ERR_MSG = array(
        self::ERR_SUCCESS       => array(self::HTTP_200, '成功'),
        self::ERR_SUCCESS_201   => array(self::HTTP_201, '成功'),
        self::ERR_SUCCESS_202   => array(self::HTTP_202, '成功'),
        self::ERR_SUCCESS_204   => array(self::HTTP_204, '成功'),

        self::ERR_URI_NOT_FOUND => array(self::HTTP_404, '资源不存在'),
        self::ERR_PARAM         => array(self::HTTP_400, '参数错误'),
        self::ERR_CHECK_LOGIN   => array(self::HTTP_400, '需要登录或者用户校验失败'),
        self::ERR_NEED_CAPTCHA  => array(self::HTTP_400, '需要验证码'),
        self::ERR_CAPTCHA       => array(self::HTTP_400, '验证码错误'),

        self::ERR_SYSTEM        => array(self::HTTP_500, '系统错误'),
        self::ERR_REQUEST_METHOD => array(self::HTTP_405, 'request method not support.'),
    );
}

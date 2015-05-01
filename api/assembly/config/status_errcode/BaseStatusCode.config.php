<?php
/**
 * 所有接口http状态码以及通用错误提示code码定义
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
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
}

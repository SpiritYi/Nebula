<?php
/**
 * 车辆错误码定义
 */

class VehicleErrCode extends BaseStatusCode {

    //车辆-2000 到 - 2999
    const ERR_EVALUATE_OPERATE = -2000;      //错误code 测试

    const ERR_VEHILCE_TOKEN_EXPIRE = -2001; //token过期

    public $err_msg = array(
        self::ERR_EVALUATE_OPERATE => array(parent::HTTP_404, '操作失败'),
        self::ERR_VEHILCE_TOKEN_EXPIRE => array(parent::HTTP_404, '登录信息已失效'),
    );
}

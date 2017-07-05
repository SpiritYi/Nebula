<?php
/**
 * 用户持股记录操作各种接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2017/05/09
 * @copyright nebula-fund.com
 */

class UserExchangeNamespace {
    
    //添加交易记录
    public static function addExchangeRecord($uid, $sid, $count, $price, $comm, $tax, $earn) {
        //生成交易记录
        require_once CODE_BASE . '/app/stock/model/ExchangeModel.class.php';
        $recordData = array(
            'uid' => $uid,
            'sid' => $sid,
            'count' => $count,
            'price' => $price,
            'direction' => ExchangeModel::DIRECTION_BUY,
            'commission' => $comm,
            'tax' => $tax,
            'earn' => $earn,
            'time' => time(),
        );
        $flag = ExchangeModel::addRecord($recordData);
        return $flag;
    }
}
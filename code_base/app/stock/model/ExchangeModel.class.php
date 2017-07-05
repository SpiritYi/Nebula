<?php
/**
 * 用户交易记录操作model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/04
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class ExchangeModel {
    const DIRECTION_BUY = 1;
    const DIRECTION_SELL = -1;
    const DIRECTION_MARGIN_SHORT_BUY = 2;           //融券买进平仓
    const DIRECTION_MARGIN_SHORT_SELL = -2;         //融券卖出

    private static $_TABLE = 'exchange';

    public static function addRecord($data) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    //获取总记录条数
    public static function getExchangeCount($uid) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('count(1) as record_count'), array(array('uid', '=', $uid)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //分页获取交易列表
    public static function getExchangeList($uid, $offset, $count) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'sid', 'count', 'delegate_price', 'strike_price',
            'direction', 'commission', 'tax', 'earn', 'time', 'desc_notice'), array(array('uid', '=', $uid)), array($offset, $count), array('time' => 'desc'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //获取最近的交易记录, 统计用
    public static function getRecentList($uid, $startT) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'uid', 'sid', 'count', 'del_id', 'delegate_price', 'strike_price',
            'direction', 'commission', 'tax', 'earn', 'time', 'desc_notice'), array(array('uid', '=', $uid), array('time', '>', $startT)),
            array(2000), array('time' => 'desc'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}

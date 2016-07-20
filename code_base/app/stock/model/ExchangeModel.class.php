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

    private static $_TABLE = 'exchange';

    public static function addRecord($data) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    //分页获取交易列表
    public static function getExchangeList($uid, $offset, $count) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'sid', 'count', 'price', 'direction',
                'commission', 'tax', 'earn', 'time'), array(array('uid', '=', $uid)), array($offset, $count), array('time' => 'desc'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}

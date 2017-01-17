<?php
/**
 * 获取金额快照
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/15
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class MoneySnapshotModel {
    private static $_TABLE = 'money_snapshot';

    public static function getRecentShot($uid, $time) {
        if (empty($uid)) {
            return array();
        }
        if (empty($time)) {
            $time = time();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'money'), array(array('uid', '=', $uid),
                array('time', '<', $time)), array(1), array('time' => 'desc'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //获取最近的快照列表
    public static function getShotList($uid, $startT) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'money', 'time'),
            array(array('uid', '=', $uid), array('time', '>', $startT)), array(1000), array('time' => 'desc'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}
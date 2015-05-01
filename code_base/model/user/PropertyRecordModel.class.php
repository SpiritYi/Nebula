<?php
/**
 * 用户资产详情表model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/05
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseMainModel.class.php';

class PropertyRecordModel {
    const TYPE_STORAGE = 1;     //申购转入
    const TYPE_TAKEOUT = 2;     //赎回转出
    const TYPE_GAIN = 3;        //盈利
    const TYPE_LOSS = 4;        //亏损

    public static $TYPE_NAME = array(
        self::TYPE_STORAGE => '申购转入',
        self::TYPE_TAKEOUT => '赎回转出',
        self::TYPE_GAIN => '盈收',
        self::TYPE_LOSS => '亏损',
    );

    private static $_TABLE = 'property_record';

    //获取用户总资产
    public static function getCountProperty($uid) {
        $handle = BaseMainModel::getDbHandle();
        // $sqlString = "SELECT sum(amount) as count FROM " . self::$_TABLE . " W"
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('sum(amount) as count'), array(array('user_id', '=', $uid)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    /**
     * 获取记录列表
     * @param $lastid int //上一次获取的最后的id
     */
    public static function getRecordList($uid, $lastid, $psize) {
        $handle = BaseMainModel::getDbHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'type', 'amount', 'notes', 'time'),
                array(array('id', '>', $lastid), array('user_id', '=', $uid)), array($psize), array('time' => 'DESC'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //获取用户记录总数
    public static function getRecordTotal($uid) {
        $handle = BaseMainModel::getDbHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('sum(*) as total'), array(array('user_id', '=', $uid)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}

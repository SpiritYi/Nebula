<?php
/**
 * 回报率操作model
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/15
 * @copyright nebula.com
 */

require_once CODE_BASE . '/app/db/BaseMainModel.class.php';

class EarningsRateModel extends BaseMainModel {
    private static $_TABLE = 'earnings_rate';

    const SH_EARN_TYPE = 1;         //上证指数
    const MY_EARN_TYPE = 2;
    const CY_EARN_TYPE = 3;         //创业板指

    public static $TYPE_NAME = array(
        self::MY_EARN_TYPE => '星云营收',
        self::SH_EARN_TYPE => '上证指数',
        self::CY_EARN_TYPE => '创业板指',
    );

    /**
     * 查询收益数据
     * @param $type int     //1.上证指数、2.星云财富
     * @param $start int    //查询的起始时间
     * @param $end int      //查询的结束时间
     * @return array
     */
    public static function getEarningsReteList($type, $start, $end) {
        $handle = self::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('*'),
                array(array('type', '=', $type), array('date_m', '>', $start), array('date_m', '<', $end)),
                array(12), array('date_m' => 'DESC'));
        $result = DBMysqlNamespace::query($handle, $sqlString);
        return array_reverse($result);
    }
}

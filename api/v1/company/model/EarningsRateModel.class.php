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

    public static function getEarningsReteList($type) {
        $handle = self::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('*'), array(array('type', '=', $type)),
                array(), array('date_m' => 'ASC'));
        $result = DBMysqlNamespace::query($handle, $sqlString);
        return $result;
    }
}

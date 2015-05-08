<?php
/**
 * 营收百分比数据model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/03
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseMainModel.class.php';

class EarningsRateBKModel {
    private static $_TABLE = 'earnings_rate';

    public static function addRateRecord($data) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}

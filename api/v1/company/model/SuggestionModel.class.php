<?php
/**
 * 用户建议model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/18
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseMainModel.class.php';

class SuggestionModel extends BaseMainModel {
    private static $_TABLE = 'suggestion';

    public static function saveSuggestion($data) {
        $handle = self::getDbHandle();
        $sqlString = SqlbuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}

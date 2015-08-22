<?php
/**
 * Stock 链接数据库入口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/21
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/DBMysqlNamespace.class.php';
require_once CONFIG . '/DBConfig.class.php';
require_once CODE_BASE . '/app/db/SqlBuilderNamespace.class.php';

class BaseStockModel {
    private static $_DB_HANDLE = false;
    private static $_DB_NAME = 'stock';

    public static function getDBHandle() {
        if (!self::$_DB_HANDLE) {
            self::$_DB_HANDLE = DBMysqlNamespace::connect(DBConfig::$STOCK_MASTER_SERVER, self::$_DB_NAME);
        }
        return self::$_DB_HANDLE;
    }
}
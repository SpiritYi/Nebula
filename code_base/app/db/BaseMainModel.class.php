<?php
/**
 * Nebula 数据库连接基类
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/15
 * @copyright nebula.com
 */

require_once CODE_BASE . '/app/db/DBMysqlNamespace.class.php';
require_once CONFIG . '/DBConfig.class.php';
require_once CODE_BASE . '/app/db/SqlBuilderNamespace.class.php';

class BaseMainModel {
    private static $_DB_HANDLE = false;
    private static $_DB_NAME = 'nebula';

    public static function getDBHandle() {
        if (!self::$_DB_HANDLE) {
            self::$_DB_HANDLE = DBMysqlNamespace::connect(DBConfig::$NEBULA_MASTER_SERVER, self::$_DB_NAME);
        }
        return self::$_DB_HANDLE;
    }
}

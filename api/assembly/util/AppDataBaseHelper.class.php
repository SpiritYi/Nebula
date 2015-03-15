<?php
/**
 * 创建handler 基类
 */

require_once CODE_BASE2 . '/util/db/DBMysqlNamespace.class.php';
require_once CODE_BASE2 . '/app/base/SqlBuilderNamespace.class.php';
require_once CODE_BASE2 . '/util/cache/CacheNamespace.class.php';
require_once GANJI_CONF . '/MemcacheConfig.class.php';

class AppDataBaseHelper {

    protected static $dbSlaveHandler = false;
    protected static $dbMasterHandler = false;
    protected static $memcacheHandler = false;

    // public function __construct(){
    //     self::$dbMasterHandler = false;
    //     self::$dbSlaveHandler  = false;
    //     self::$memcacheHandler = false;
    // }

    /**
     * 创建数据库handler
     * @param $model string    业务线名，如移动'MOB'
     * @param $DB string        数据库名
     * @param $slave bool       是否从库，默认从库
     */
    public static function getDbHandler($model, $DB, $slave = true) {
        if ($slave === true) {
            $MODEL = 'SERVER_'.strtoupper($model).'_SLAVE';
            if (!isset(DBConfig::$$MODEL)) {
                return false;
            }
            if(self::$dbSlaveHandler == false) {
                self::$dbSlaveHandler = DBMysqlNamespace::createDBHandle(DBConfig::$$MODEL, $DB, DBConstNamespace::ENCODING_UTF8);
                if (FALSE === self::$dbSlaveHandler) {
                    Logger::logError(sprintf('SLAVEDB TIME=%s FILE=%s LINE=%s MESSAGE=%s', date('Y-m-d H:i:s'), __FILE__, __LINE__, 'slave db handler create fail'), 'db');
                }
                return self::$dbSlaveHandler;
            }
            return self::$dbSlaveHandler;
        }

        if (self::$dbMasterHandler == false) {
            $MODEL = 'SERVER_'.strtoupper($model).'_MASTER';
            if (!isset(DBConfig::$$MODEL)) {
                return false;
            }
            self::$dbMasterHandler = DBMysqlNamespace::createDBHandle(DBConfig::$$MODEL, $DB, DBConstNamespace::ENCODING_UTF8);
            if (FALSE === self::$dbMasterHandler) {
                Logger::logError(sprintf('MASTERDB TIME=%s FILE=%s LINE=%s MESSAGE=%s', date('Y-m-d H:i:s'), __FILE__, __LINE__, 'master db handler create fail'), 'db');
            }
            return self::$dbMasterHandler;
        }
        return self::$dbMasterHandler;
    }

    public static function getMcHandler() {
        if (self::$memcacheHandler == false) {
            self::$memcacheHandler = CacheNamespace::createCache(CacheNamespace::MODE_MEMCACHE, MemcacheConfig::$GROUP_MOBILE);
            if (!self::$memcacheHandler) {
                Logger::logError(sprintf('MEMCACHE TIME=%s FILE=%s LINE=%s MESSAGE=%s', date('Y-m-d H:i:s'), __FILE__, __LINE__, 'memcache handler create fail'), 'moblie');
            }
        }
        return self::$memcacheHandler;
    }
}

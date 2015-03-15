<?php
/**
 * h5 推送用户model
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2014/11/20
 * @copyright ganji.com
 */

require_once GANJI_CONF . '/DBConfig.class.php';
require_once CODE_BASE2 . '/util/db/DBMysqlNamespace.class.php';
require_once CODE_BASE2 . '/app/base/SqlBuilderNamespace.class.php';

class ClientHfPushUser {
    protected static $dbSlaveHandler = false;
    protected static $dbMasterHandler = false;
    private static $_DB_NAME = 'ana_mob';

    public function getDbHandler($slave = true) {
        if ($slave === true) {
            if (self::$dbSlaveHandler == false) {
                self::$dbSlaveHandler = DBMysqlNamespace::createDBHandle ( DBConfig::$ANA_MOB_SLAVE, self::$_DB_NAME, DBConstNamespace::ENCODING_UTF8 );
                if (FALSE === self::$dbSlaveHandler) {
                    Logger::logError ( sprintf ( 'SLAVEDB TIME=%s FILE=%s LINE=%s MESSAGE=%s', date ( 'Y-m-d H:i:s' ), __FILE__, __LINE__, 'slave db handler create fail' ), 'db' );
                }
                return self::$dbSlaveHandler;
            }
            return self::$dbSlaveHandler;
        } else {
            if (self::$dbMasterHandler == false) {
                self::$dbMasterHandler = DBMysqlNamespace::createDBHandle ( DBConfig::$ANA_MOB_MASTER, self::$_DB_NAME, DBConstNamespace::ENCODING_UTF8 );
                if (FALSE === self::$dbMasterHandler) {
                    Logger::logError ( sprintf ( 'MASTERDB TIME=%s FILE=%s LINE=%s MESSAGE=%s', date ( 'Y-m-d H:i:s' ), __FILE__, __LINE__, 'master db handler create fail' ), 'db' );
                }
                return self::$dbMasterHandler;
            }
            return self::$dbMasterHandler;
        }
    }

    private static $_TABLE = 'client_h5_push_user';

    /**
     * 添加要推送的用户
     * @param $installId int    客户端安装id
     * @param $userType int     活动对应的推送用户类型
     */
    public static function addPushUser($param) {
        //先校验用户是否已经添加了
        $existFlag = self::getUserPush($param['install_id'], $param['user_type']);
        if (!empty($existFlag))
            return true;

        $handle = self::getDbHandler(false);
        $data = array(
            'install_id' => $param['install_id'],
            'customer_id' => $param['customer_id'],
            'access_time' => time(),
            'create_time' => time(),
            'user_type' => $param['user_type'],
        );
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $result = DBMysqlNamespace::execute($handle, $sqlString);
        return $result;
    }

    //去掉一个活动的订阅
    public static function cancelPush($installId, $userType) {
        $handle = self::getDbHandler(false);
        $sqlString = SqlBuilderNamespace::buildDeleteSql(self::$_TABLE, array(array('install_id', '=', $installId), array('user_type', '=', $userType)));
        $result = DBMysqlNamespace::execute($handle, $sqlString);
        return $result;
    }

    //获取某个活动是否订阅
    public static function getUserPush($installId, $userType) {
        $handle = self::getDbHandler(true);
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'customer_id', 'install_id', 'user_type'), 
                array(array('install_id', '=', $installId), array('user_type', '=', $userType)));
        $result = DBMysqlNamespace::query($handle, $sqlString);
        if (empty($result)) {   //从库没有查主库
            $masterHandle = self::getDbHandler(false);
            $result = DBMysqlNamespace::query($masterHandle, $sqlString);
        }
        return $result;
    }
}
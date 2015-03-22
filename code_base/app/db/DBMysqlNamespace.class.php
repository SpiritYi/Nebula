<?php
/**
 * 数据库操作
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/09
 * @copyright nebula.com
 */

class DBConstNamespace {
    const HANDLE_PING = true;
    const NOT_HANDLE_PING = FALSE;
}

class DBMysqlNamespace {

    private static $_HANDLE_PING = true;

    //创建数据库链接
    public static function connect($server, $database) {
        require_once CONFIG . '/DBConfig.class.php';
        if (DBConfig::IS_DEV_ENV) {     //开发测试环境数据库连test后缀库
            $database = $database . '_test';
        }
        $handle = mysqli_connect($server['host'], $server['user'], $server['password'], $database);
        if (mysqli_connect_errno($handle)) {
            Logger::logError('Connect database error.' . var_export($server, true) . $database, 'db.connect');
        }
        //只支持utf8
        $handle->set_charset("utf8");
        return $handle;
    }

    /// 执行sql语句， 该语句必须是insert, update, delete, create table, drop table等更新语句
    /// @param[in] handle $handle, 操作数据库的句柄
    /// @param[in] string $sql, 具体执行的sql语句
    /// @return TRUE:表示成功， FALSE:表示失败
    public static function execute(&$handle, $sql) {
        if (!self::_checkHandle($handle))
            return FALSE;
        // $tm = DateTimeNamespace::getMicrosecond();
        if (self::mysqliQueryApi($handle, $sql)) {
            // $tm_used = intval( (DateTimeNamespace::getMicrosecond() - $tm) / 1000);
            // if( $tm_used > SLOW_QUERY_MIN && rand(0,SLOW_QUERY_SAMPLE) == 1) {
                // self::logWarn( "ms=$tm_used, SQL=$sql", 'mysqlns.slow' );
            // }
            return TRUE;
        }
        // to_do, execute sql语句失败， 需要记log
        Logger::logError( "SQL Error: $sql, errno=" . self::getLastError($handle), 'mysqlns.sql');

        return FALSE;
    }

    /// 执行insert sql语句，并获取执行成功后插入记录的id
    /// @param[in] handle $handle, 操作数据库的句柄
    /// @param[in] string $sql, 具体执行的sql语句
    /// @return FALSE表示执行失败， 否则返回insert的ID
    public static function insertAndGetID(&$handle, $sql) {
        if (!self::_checkHandle($handle))
            return false;
        do {
            if (self::mysqliQueryApi($handle, $sql) === FALSE)
                break;
            if (($result = self::mysqliQueryApi($handle, 'select LAST_INSERT_ID() AS LastID')) === FALSE)
                break;
            $row = mysqli_fetch_assoc($result);
            $lastid = $row['LastID'];
            mysqli_free_result($result);
            return $lastid;
        } while (FALSE);
        // to_do, execute sql语句失败， 需要记log
        Logger::logError( "SQL Error: $sql, errno=" . self::getLastError($handle), 'mysqlns.sql');
        return FALSE;
    }

    /// 将所有结果存入数组返回
    /// @param[in] handle $handle, 操作数据库的句柄
    /// @param[in] string $sql, 具体执行的sql语句
    /// @return FALSE表示执行失败， 否则返回执行的结果, 结果格式为一个数组，数组中每个元素都是mysqli_fetch_assoc的一条结果
    public static function query(&$handle, $sql) {
        if (!self::_checkHandle($handle))
            return FALSE;
        do {
            // $tm = DateTimeNamespace::getMicrosecond();
            if (($result = self::mysqliQueryApi($handle, $sql)) === FALSE){
                break;
            }
            // $tm_used = intval( (DateTimeNamespace::getMicrosecond() - $tm) / 1000);
            // if( $tm_used > SLOW_QUERY_MIN && rand(0,SLOW_QUERY_SAMPLE) == 1) {
                // self::logWarn( "ms=$tm_used, SQL=$sql", 'mysqlns.slow' );
            // }
            $res = array();
            while($row = mysqli_fetch_assoc($result)) {
                $res[] = $row;
            }
            mysqli_free_result($result);
            return $res;
        } while (FALSE);
        // to_do, execute sql语句失败， 需要记log
        Logger::logError( "SQL Error: $sql, errno=" . self::getLastError($handle), 'mysqlns.sql');

        return FALSE;
    }

    public static function mysqliQueryApi(&$handle, $sql) {
        do {
            $result = mysqli_query($handle, $sql);
            if ($result === FALSE) {
                if (!is_object($handle)) return false;

                //强制指定不能重连
                if (self::$_HANDLE_PING === DBConstNamespace::NOT_HANDLE_PING) {
                    return false;
                }
                //cli模式 或者 指定了重练
                if (PHP_SAPI === 'cli' || self::$_HANDLE_PING === DBConstNamespace::HANDLE_PING) {
                } else {
                    return false;
                }

                if (self::_reconnectHandle($handle)) {
                    $result = mysqli_query($handle, $sql);;
                }
                if ($result === FALSE) {
                    break;
                }
            }
            return $result;
        } while (0);
        return false;
    }

        /// 得到最近一次操作错误的信息
    /// @param[in] handle $handle, 操作数据库的句柄
    /// @return FALSE表示执行失败， 否则返回 'errorno: errormessage'
    public static function getLastError($handle) {
        if(($handle)) {
            return mysqli_errno($handle).': '.mysqli_error($handle);
        }
        return FALSE;
    }

    private static function _reconnectHandle(&$handle) {
        if (!is_object($handle)) return false;
        $thread_id = $handle->thread_id;

        //MySQL server has gone away
        $errno = @mysqli_errno($handle);
        if ($thread_id > 0 && in_array($errno, array(2006, 2013)) && isset(self::$_HANDLE_THREAD_CONFIGS[$thread_id])) {
            //使用 dbconfig 重新连接
            $db_configs = self::$_HANDLE_THREAD_CONFIGS[$thread_id];
            self::releaseDBHandle($handle);
            $handle = DBMysqlNamespace::createDBHandle($db_configs, $db_configs['db_name'], $db_configs['encoding']);
            $ret = is_object($handle) ? true : false;
            return $ret;
        }
        return false;
    }

    /**
     * @brief 检查handle
     * @param[in] handle $handle, 操作数据库的句柄
     * @return boolean true|成功, false|失败
     */
    private static function _checkHandle($handle, $log_category = 'mysqlns.handle') {
        if (!is_object($handle) || $handle->thread_id < 1) {
            Logger::logError(sprintf("handle Error: handle='%s'", var_export($handle, true)), $log_category);
            return false;
        }
        return true;
    }
}

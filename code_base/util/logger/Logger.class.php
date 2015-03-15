<?php
/**
 * 日志记录类
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/08
 * @copyright nebula.com
 */

class Logger {
    public static function logInfo() {
        self::_saveLog(__FUNCTION__, $msg, $category);
    }

    public static function logWarn() {
        self::_saveLog(__FUNCTION__, $msg, $category);
    }

    public static function logError($msg, $category) {
        self::_saveLog(__FUNCTION__, $msg, $category);
    }

    private static function _saveLog($func, $msg, $category) {
        $saveData = array(
            'time' => date('Y/m/d H:i:s'),
            'timestamp' => time(),
            'category' => $category,
            'msg' => $msg,
        );
        require_once CONFIG . '/DBConfig.class.php';
        $logFile = sprintf('%s.log', strtolower($func));
        file_put_contents(DBConfig::LOG_PATH . $logFile, implode("\t", $saveData) . "\n", FILE_APPEND);
    }
}
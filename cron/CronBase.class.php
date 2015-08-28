<?php
/**
 * cron 文件基类
 * @author Yihong Chen <jinglinglingyueyue@gmail.com>
 * @version 2015/08/25
 * @copyright immomo.com
 */

require_once dirname(__FILE__) . '/../config/DirConfig.inc.php';

require_once CODE_BASE . '/util/logger/Logger.class.php';

abstract class CronBase {
    public function __construct() {
        if (empty($_SERVER['argv'][1])) {
            return false;
        }
        $cycleConfigFlag = $this->checkCycleConfig();
        $rParam = $_SERVER['argv'][1];
        if ($rParam == '-t') {

        } else if ($rParam == '-r') {
            if (!$cycleConfigFlag) {
                exit;
            }
        }
        $this->run();
    }

    //校验设置的运行时间周期是否正确
    public function checkCycleConfig() {
        $cycle = $this->setCycleConfig();
        $configArr = explode(' ', $cycle);
        if (!is_array($configArr) || count($configArr) != 4) {
            echo 'Cycle config error.';
            return false;
        }
        //解析时间设置
        $typeMap = array(
            0 => 'i',
            1 => 'H',
            2 => 'd',
            3 => 'm',
        );
        $flag = true;
        foreach ($configArr as $key => $cStr) {
            $flag = $this->_compileCycle($cStr, $typeMap[$key]);
            if (!$flag) {
                return false;
            }
        }
        return true;
    }
    //匹配单个时间节点格式
    private function _compileCycle($cStr, $type) {
        $current = (int)date($type);
        if ($cStr == '*') {
            return true;
        }
        //指定时间点模式，如11， 34
        if (preg_match('/^[\d]+$/', $cStr)) {
            return $cStr == $current;
        }
        //指定时间段，如5-8， 10-23
        if (preg_match('/^([\d]+)-([\d]+)$/', $cStr, $arr)) {
            return $arr[1] <= $current && $current <= $arr[2];
        }
        //每隔一段时间，如 */3, */12
        if (preg_match('/^(\*)\/([\d]+)$/', $cStr, $arr)) {
            return $current % $arr[2] == 0;
        }
        //时间段内每隔一定时间，如9-15/2, 10-20/5
        if (preg_match('/^([\d]+)-([\d]+)\/([\d]+)$/', $cStr, $arr)) {
            return ($arr[1] <= $current && $current <= $arr[2]) && (($current - $arr[1]) % $arr[3] == 0);
        }
        return false;
    }

    /**
     * 设置运行时间周期，参数crontab 时间设置，这里只支持四位 '* * * *'
     * 分 小时 日 月
     */
    abstract public function setCycleConfig();
    abstract public function run();
}
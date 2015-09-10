<?php
/**
 * 总代脚本，每分钟启动，负责启动cron 目录下其他 *.cron.php 的脚本
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/28
 * @copyright nebula-fund.com
 */

require_once dirname(__FILE__) . '/../config/DirConfig.inc.php';

require_once CODE_BASE . '/util/logger/Logger.class.php';

class Crontab {
    public function run() {
        $this->launchCronScript();
    }

    public function launchCronScript() {
        $this->traverseDir(CRON);
    }

    //递归遍历
    public function traverseDir($path) {
        if (file_exists($path)) {
            $listArr = scandir($path);
            foreach ($listArr as $item) {
                if (in_array($item, ['.', '..'])) {
                    continue;
                }
                $curPath = $path . '/' . $item;
                if (is_dir($curPath)) {
                    $this->traverseDir($curPath);   //递归子文件夹
                } else {
                    $this->launch($curPath);    //启动文件
                }
            }
        } else {
            echo '路径不存在';
        }
    }

    //启动一个cron脚本文件
    public function launch($filePath) {
        $info = pathinfo($filePath);
        //过滤脚本
        if ($info['basename'] == $_SERVER['PHP_SELF'] || !preg_match('/^.*\.cron.php$/', $info['basename'])) {
            return false;
        }
        //检查脚本是否没运行完
        $psStr = shell_exec(sprintf('ps aux | grep -v grep | grep %s', $info['basename']));
        if (!empty($psStr)) {
            Logger::logWarn($info['basename'] . ', still run.', 'crontab_still');
            /*
            $psArr = explode("\t", preg_replace('/ +/', "\t", $psStr));
            if (is_numeric($psArr[1])) {
                //判断进程开始启动时间
                $tStr = shell_exec('ps -eo pid,lstart | grep ' . $psArr[1]);
                preg_match('/^[\d]+(.*)$/', $tStr, $tArr);
                if (strtotime($tArr[1]) > time() - 10) {   //超过30分钟脚本kill 掉
                    shell_exec('kill -9 ' . $psArr[1]);     //先杀掉跑的进程
                    Logger::logError('kill. ' . $psStr, 'crontab_kill');
                }
            } else {
                Logger::logError('kill failed. ' . $psStr, 'crontab_kill_error');
                return false;
            } 
            */
        }
        shell_exec('php ' . $filePath . ' -r > /dev/null &');
        Logger::logInfo('run ' . $info['basename'], 'crontab_launch');
    }
}

$instance = new Crontab();
$instance->run();
<?php
/**
 * 更新主板指数
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/08
 * @copyright nebula-fund.com
 */

require_once dirname(__FILE__) . '/../../CronBase.class.php';

class CollectMarketPoint extends CronBase {
    public function setCycleConfig() {
        return '* 9-15 * *';    //上午9点 - 下午3点 每分钟请求
    }

    public function run() {
        Logger::logInfo(__CLASS__ . ', start', 'cron_collect_point_run');

        $this->collect();

        Logger::logInfo(__CLASS__ . ', end', 'cron_collect_point_run');
    }

    public function collect() {
        //获取上证指数
        require_once CODE_BASE . '/util/http/HttpUtil.class.php';
        $url = sprintf(DBConfig::STOCK_COMPANY_DATA_URL, 'sh000001');
        $str = HttpUtil::curlget($url);
        if (empty($str)) {
            return false;
        }
        require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
        $info = StockCompanyNamespace::parseData($str);
        if (empty($info)) {
            return false;
        }
        $dayDate = date('Ymd', $info['time']);
        $dayData = array(
            'sid' => $info['sid'],
            'date' => $dayDate,
            'opening_price' => $info['opening_price'],
            'closing_price' => $info['price'],
            'highest' => $info['highest'],
            'lowest' => $info['lowest'],
            'time' => $info['time'],
        );
        //查询是否有数据，没有做添加
        require_once CODE_BASE . '/app/stock/model/StockPointModel.class.php';
        $dayRecord = StockPointModel::selectDayPoint($dayData['sid'], $dayDate);
        if (empty($dayRecord)) {
            $flag = StockPointCronModel::addDayData($dayData);
        } else {
            $udpate = array();
            foreach ($dayRecord[0] as $field => $value) {
                if (isset($dayData[$field]) && $dayData[$field] != $value) {
                    $udpate[$field] = $dayData[$field];
                }
            }
            if (!empty($udpate)) {
                $flag = StockPointCronModel::updateDayData($dayData['sid'], $dayDate, $udpate);
            } else {
                $flag = true;
            }
        }
        if (!$flag) {
            Logger::logInfo($str, 'cron_collect_update_error');
        }
    }
}

$instance = new CollectMarketPoint();

//cron 脚本使用的model
class StockPointCronModel {
    private static $_TABLE = 'stock_point';

    //添加每日记录
    public static function addDayData($data) {
        if (empty($data['sid'])) {
            return false;
        }
        if (empty($data['date'])) {
            $data['date'] = date('Ymd');
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::BuildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    //更新数据
    public static function updateDayData($sid, $date, $data) {
        if (empty($sid)) {
            return false;
        }
        if (empty($date)) {
            $date = date('Ymd');
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::BuildUpdateSql(self::$_TABLE, $data, array(array('sid', '=', $sid),
                array('date', '=', $date)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}
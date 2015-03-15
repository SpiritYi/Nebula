<?php
/**
 * 积分商城清理10分钟过期未处理订单
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2015/01/30
 * @copyright ganji.com
 */

require_once dirname(__FILE__) . '/../../../../app/push/model/CronParent.class.php';
require_once CODE_BASE2 . '/app/mobile_client/model/credit/baseCreditModel.class.php';

class ClearExpireOrder extends  CronParent {
    public function __construct() {
        parent::__construct();

        $this->rate = 0.004;
        self::addMailLog(array('start' => '清理过期未处理订单'));
    }

    public function run() {
        $this->setOrder();

        self::addMailLog(array('end' => '执行结束'));
        self::sendMailLog('清理过期订单脚本邮件', array(), $this->rate);
    }

    public function setOrder() {
        require_once CODE_BASE2 . '/../mobile_client/tools/ClientRedisCache.class.php';
        ClientRedisCache::setRedisConfig('GROUP_APPCREDIT');
        $redisInstance = ClientRedisCache::getRedisHandler();

        $lastId = 0;
        $pageSize = 1000;
        $flag = true;
        $runTime = time();
        $total = 0;
        while ($flag) {
            $expireOrder = CronCreditProductOrderModel::selectValidOrder($lastId, $pageSize);
            if (empty($expireOrder))
                break;
            if (count($expireOrder) < $pageSize)
                $flag = false;

            foreach ($expireOrder as $orderItem) {
                $total ++;
                //把订单置过期
                $orderData = array(
                    'status' => 2,  //已过期
                    'update_time' => $runTime,
                );
                $orderRes = CronCreditProductOrderModel::updateOrder($orderItem['id'], $orderData);
                $orderRes = true;
                if (!$orderRes)
                    continue;
                //重置库存数据
                $inventoryData = array(
                    'status' => 0,
                    'update_time' => $runTime,
                );
                $inventoryRes = CronCreditProductInventoryModel::updateInventory($orderItem['inventory_id'], $inventoryData);
                $inventoryRes = true;
                if (!$inventoryRes)
                    continue;
                //回补库存
                $inventoryListKey = sprintf('product_inventory_id_%s', $orderItem['product_id']);
                $redisRes = $redisInstance->lpush($inventoryListKey, $orderItem['inventory_id']);
                //回撤售卖数量
                $soldKey = sprintf('credit_product_sold_%s', $orderItem['product_id']);
                $soldTotal = $redisInstance->get($soldKey);
                $redisRes = $redisInstance->set($soldKey, $soldTotal - 1);
                $redisRes = $redisInstance->expireAt($soldKey, strtotime(date('Y/m/d', strtotime('+1 day'))) - 5);
            }
        }
        var_dump('处理总数:' . $total);
        self::addMailLog(array('total' => '清理总数:' . $total));
    }
}

//cron 专用model
class CronCreditProductOrderModel {
    private static $_TABLE_NAME = 'credit_product_order';

    //获取等待支付的订单，处理过期未处理订单
    public static function selectValidOrder($lastId, $pageSize) {
        $handle = baseCreditModel::getDbHandler(true);
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE_NAME, array('id', 'user_id', 'product_id', 'inventory_id', 'status'),
                array(array('status', '=', 0), array('create_time', '<', time() - 10 * 60), array('id', '>', $lastId)), array($pageSize));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    public static function updateOrder($id, $data) {
        $handle = baseCreditModel::getDbHandler(false);
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE_NAME, $data, array(array('id', '=', $id)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}
class CronCreditProductInventoryModel {
    private static $_TABLE_NAME = 'credit_product_inventory';

    public static function updateInventory($id, $data) {
        $handle = baseCreditModel::getDbHandler(false);
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE_NAME, $data, array(array('id', '=', $id)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}

$instance = new ClearExpireOrder();
$instance->run();

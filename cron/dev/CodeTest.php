<?php
/**
 * 直接调用代码, 测试用脚本
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/02
 * @copyright nebula-fund.com
 */

require_once dirname(__FILE__) . '/../../config/DirConfig.inc.php';
require_once CODE_BASE . '/app/stock/model/StockCompanyModel.class.php';

class CodeTest {
    public function run() {
        $this->dbTest();
        // $this->msg();
//        $this->info();
//        $this->esResquest();
    }

    public function dbTest() {
        $startT = microtime(true);
        for ($i = 0; $i < 10; $i ++) {
            $count = StockCompanyModel::getCompanyCount();
            var_dump($count);
        }
        $endT = microtime(true);
        var_dump('time:' . ($endT - $startT) . 's');
    }

    public function esResquest() {
//        require_once CODE_BASE . '/util/http/EsUtil.class.php';
//        $res = EsUtil::curlRequest('http://es.nebula-fund.com/leonebula-test/backend/1/_update', 'POST', json_encode(['doc' => ['phone' => '123']]));
//        var_dump($res);exit;

        require_once CODE_BASE . '/app/es/LeoEsNamespace.class.php';
        $data = array(
            'id' => '1003',
            'name' => '肌肉试剂',
            'address' => '北京朝阳SOHO',
            'phone' => '13287638298',
            'keyword' => '肌肉 染色',
        );
//        $res = LeoEsNamespace::addRecord('1003', $data);

//        $res = LeoEsNamespace::delRecord('*');

        $res = LeoEsNamespace::updateRecord('1002', ['address' => '上海虹桥机场T2 102室']);
        var_dump($res);
    }

    public function msg() {
        require_once CODE_BASE . '/app/stock/StockMsgNamespace.class.php';
        $res = StockMsgNamespace::sendMsg('1', 'test title', 'test content');
        var_dump($res);
    }

    public function info() {
        require_once CODE_BASE . '/app/stock/model/MoneySnapshotModel.class.php';
        $res = MoneySnapshotModel::getRecentShot('1', strtotime(date('Y/m/d')) - 1);
        var_dump($res);
    }
}

$instance = new CodeTest();
$instance->run();

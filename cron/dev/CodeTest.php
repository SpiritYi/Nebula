<?php
/**
 * 直接调用代码, 测试用脚本
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/02
 * @copyright nebula-fund.com
 */

require_once dirname(__FILE__) . '/../../config/DirConfig.inc.php';

class CodeTest {
    public function run() {
        // $this->msg();
        $this->info();
    }

    public function msg() {
        require_once CODE_BASE . '/app/stock/StockMsgNamespace.class.php';
        $res = StockMsgNamespace::sendMsg('1', 'test title', 'test content');
        var_dump($res);
    }

    public function info() {
        require_once CODE_BASE . '/util/http/HttpUtil.class.php';
        require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
        $res = StockCompanyNamespace::getCompanyMarketInfo('600111');
        var_dump($res);
    }
}

$instance = new CodeTest();
$instance->run();
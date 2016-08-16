<?php
/**
 * 用户交易记录页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/06/10
 * @copyright nebula_fund.com
 */

class ExchangePage extends StockMaster {

    public $page = 1;
    public $exchangeList = array();             //委托列表list
    public $indexStart = 1;                     //分页导航索引
    public $indexEnd = 1;
    public $pageTotal = 1;
    public $pageUrlTmp = '/stock/exchange?page=';       //页面url 模板

    public function loadHead() {
        $this->staExport('<title>交易记录</title>');
    }

    public function action() {
        require_once CODE_BASE . '/app/stock/UserStockNamespace.class.php';
        $this->page = HttpUtil::getParam('page', 1);
        if ($this->page < 1) {
            $this->page = 1;
        }
        $pageCount = 10;             //每页最多记录条数
        $indexSpread = 3;
        require_once CODE_BASE . '/app/stock/model/ExchangeModel.class.php';
        $recordCount = ExchangeModel::getExchangeCount($this->userInfo['uid']);
        $count = $recordCount[0]['record_count'];
        $this->pageTotal = intval($count / $pageCount) + ($count % $pageCount > 0 ? 1 : 0);
        if ($this->page > $this->pageTotal) {
            $this->page = $this->pageTotal;
        }
        $this->indexStart = $this->page - $indexSpread < 1 ? 1 : $this->page - $indexSpread;
        $this->indexEnd = $this->page + $indexSpread > $this->pageTotal ? $this->pageTotal : $this->page + $indexSpread;

        $this->exchangeList = UserStockNamespace::getUserExchangeList($this->userInfo['uid'], $this->page - 1, $pageCount);
        $this->render('/stock/exchange.php');
    }
}

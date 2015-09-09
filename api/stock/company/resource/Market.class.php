<?php
/**
 * 交易市场信息
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/08
 * @copyright nebula-fund.com
 */

class MarketRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/market/status/' => array(
                'GET' => 'getMarketStatus',     //获取市场状态
            ),
        );
    }

    public function getMarketStatusAction() {
        //判断是否交易时间
        require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
        $this->output(200, ['is_exchange' => StockCompanyNamespace::isExchangeHour()]);
    }
}

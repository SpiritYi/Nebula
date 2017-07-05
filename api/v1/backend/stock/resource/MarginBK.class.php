<?php
/**
 * 信用交易接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2017/05/08
 * @copyright nebula-fund.com
 */

class MarginBKRes extends ResourceBase {
    
    public function setUriMatchConfig() {
        return array(
            '/marginbk/short_selling' => array(
                'POST' => 'shortSelling',
            ),
        );
    }
    
    public function shortSellingAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $uid = HttpUtilEx::getParam('uid');
        $sid = HttpUtilEx::getParam('sid');
        $price = HttpUtilEx::getParam('price');
        $count = HttpUtilEx::getParam('count');
        if ($price <= 0) {
            $this->output(400, '', '价格不正确');
        }
        if ($count <= 0 || $count % 100 != 0) {
            $this->output(400, '', '股数不正确。只能是100的倍数');
        }
        require_once CODE_BASE . '/app/user/StockUserNamespace.class.php';
        $userInfo = StockUserNamespace::getUserInfoById($uid);
        if (empty($userInfo)) {
            $this->output(400, '', '用户查找失败');
        }
        require_once CODE_BASE . '/app/stock/model/StockCompanyModel.class.php';
        $stockInfo = StockCompanyModel::getBatchInfo(array($sid));
        if (empty($stockInfo)) {
            $this->output(400, '', '股票信息查找失败');
        }
        require_once CODE_BASE . '/app/stock/UserMarginStockNamespace.class.php';
        $commRate = 0.0007;     //万7佣金费率
        $taxRate = 0.001;       //千分之一印花税
        $tradeMoney = $count * $price;
        $cost = $tradeMoney * (-1 + $commRate + $taxRate);       //万分之7 佣金，千分一手续费
        $res = UserMarginStockNamespace::setUserHoldings($uid, $sid, -1 * $count, $price, round($cost, 2));
        if ($res) {
            //生成交易记录
            require_once CODE_BASE . '/app/stock/model/ExchangeModel.class.php';
            $recordData = array(
                'uid' => $uid,
                'sid' => $sid,
                'count' => $count,
                'delegate_price' => $price,
                'strike_price' => $price,
                'direction' => ExchangeModel::DIRECTION_MARGIN_SHORT_SELL,
                'commission' => $tradeMoney * $commRate,
                'tax' => $tradeMoney * $taxRate,
                'earn' => 0,
                'time' => time(),
            );
            ExchangeModel::addRecord($recordData);
            $this->output(200, '', '操作成功');
        } else {
            $this->output(500, '', '服务器处理错误');
        }
    }
}
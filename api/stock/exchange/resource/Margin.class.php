<?php
/**
 * 信用交易接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2017/05/09
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/user/StockUserNamespace.class.php';
require_once CODE_BASE . '/app/stock/UserMarginStockNamespace.class.php';
require_once CODE_BASE . '/app/stock/model/ExchangeModel.class.php';

class MarginRes extends ResourceBase {
    
    public function setUriMatchConfig() {
        return array(
            '/margin/short/' => array(
                'POST' => 'shortSelling',       //融券卖出
            ),
            '/margin/short/:msrid/' => array(
                'DELETE' => 'shortClose',       //融券平仓
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
        $userInfo = StockUserNamespace::getUserInfoById($uid);
        if (empty($userInfo)) {
            $this->output(400, '', '用户查找失败');
        }
        require_once CODE_BASE . '/app/stock/model/StockCompanyModel.class.php';
        $stockInfo = StockCompanyModel::getBatchInfo(array($sid));
        if (empty($stockInfo)) {
            $this->output(400, '', '股票信息查找失败');
        }
        $commRate = UserMarginStockNamespace::MARGIN_COMMSION;
        $taxRate = UserMarginStockNamespace::MARGIN_TAX;
        $tradeMoney = $count * $price;
        $cost = $tradeMoney * (-1 + $commRate + $taxRate);       //万分之7 佣金，千分一手续费
        $res = UserMarginStockNamespace::setUserHoldings($uid, $sid, -1 * $count, $price, round($cost, 2));
        if ($res) {
            //生成交易记录
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
    
    public function shortCloseAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请刷新页面，重新登录');
        }
        $msrid = HttpUtilEx::getParam('msrid');
        $price = HttpUtilEx::getParam('price');
        $count = HttpUtilEx::getParam('count');
        if ($price <= 0) {
            $this->output(400, '', '价格不正确');
        }
        if ($count <= 0 || $count % 100 != 0) {
            $this->output(400, '', '股数不正确。只能是100的倍数');
        }
        require_once CODE_BASE . '/app/stock/model/UserMarginStockModel.class.php';
        $holdings = UserMarginStockModel::selectHoldingsById($user['uid'], $msrid);
        if (empty($holdings)) {
            $this->output(400, '', '持股记录不存在');
        }
        $holdingsItem = $holdings[0];
        if ($count >= $holdingsItem['count']) {         //全部平仓
            $tradeMoney = $count * $price;
            $commRate = UserMarginStockNamespace::MARGIN_COMMSION;
            $earn = ($tradeMoney * (1 + $commRate) + $holdingsItem['cost']) * -1;
            //修改用户资产
            StockUserNamespace::setUserInfo($user['uid'], [
                'money' => $user['money'] + $earn,
                'usable_money' => $user['money'] + $earn,
            ]);
            
            //生成交易记录
            $recordData = array(
                'uid' => $user['uid'],
                'sid' => $holdingsItem['sid'],
                'count' => $count,
                'delegate_price' => $price,
                'strike_price' => $price,
                'direction' => ExchangeModel::DIRECTION_MARGIN_SHORT_BUY,
                'commission' => $tradeMoney * $commRate,
                'tax' => 0,
                'earn' => $earn,
                'time' => time(),
            );
            ExchangeModel::addRecord($recordData);
            
            //清除持股记录
            UserMarginStockModel::deleteHoldings($user['uid'], $msrid);
            $this->output(200, '', '操作成功');
        } else {        //部分平仓
            
        }
    }
}
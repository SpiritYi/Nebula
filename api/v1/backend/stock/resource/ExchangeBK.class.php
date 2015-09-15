<?php
/**
 * 后台操作股票交易接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/04
 */

class ExchangeBKRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/exchangebk/' => array(
                'POST' => 'buyStock',   //操作用户购买股票
            ),
        );
    }

    public function buyStockAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $uid = HttpUtil::getParam('uid');
        $sid = HttpUtil::getParam('sid');
        $price = HttpUtil::getParam('price');
        $count = HttpUtil::getParam('count');
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
        //计算总花费金额
        $value = $count * $price;
        $commission = $value * 0.001;   //固定千分之一的佣金
        $cost = $value + $commission;   //总花费
        //校验资产
        if ($userInfo['usable_money'] < $cost) {
            $this->output(400, '', '资金不足');
        }
        //生成交易记录
        require_once CODE_BASE . '/app/stock/model/ExchangeModel.class.php';
        $recordData = array(
            'uid' => $uid,
            'sid' => $sid,
            'count' => $count,
            'price' => $price,
            'direction' => ExchangeModel::DIRECTION_BUY,
            'commission' => $commission,
            'tax' => 0,
            'earn' => 0,
            'time' => time(),
        );
        $flag = ExchangeModel::addRecord($recordData);
        if (!$flag) {
            $this->output(500, '', '生成交易记录失败');
        }
        //添加持股
        require_once CODE_BASE . '/app/stock/UserStockNamespace.class.php';
        $opFlag = UserStockNamespace::setUserHolding($uid, $sid, $count, $cost, true);
        if (!$opFlag) {
            $this->output(500, '', '生成持股记录失败');
        }
        //更新用户资产
        require_once CODE_BASE . '/app/user/model/StockUserInfoModel.class.php';
        $userUpdate = array(
            'money' => $userInfo['money'] - $cost,
            'usable_money' => $userInfo['usable_money'] - $cost,
        );
        $changeFlag = StockUserInfoModel::updateUserInfo($userInfo['uid'], $userUpdate);
        if (!$changeFlag) {
            $this->output(500, '', '减记用户资产失败');
        }
        $this->output(200, '', '操作成功');
    }
}

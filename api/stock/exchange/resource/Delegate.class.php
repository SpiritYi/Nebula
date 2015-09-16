<?php
/**
 * 用户交易委托
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/16
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/model/DelegateListModel.class.php';

class DelegateRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/delegate/' => array(
                'POST' => 'createDelegate',         //提交委托记录
            ),
            '/delegate/:dlgid/' => array(
                'DELETE' => 'cancelDelegate',       //撤销委托
            ),
        );
    }

    public function createDelegateAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请刷新页面，重新登录');
        }

        $direction = HttpUtil::getParam('direction');
        if (!in_array($direction, [1, -1])) {
            $this->output(400, '', '买卖方向数据错误');
        }
        //验证上市公司信息
        $sid = HttpUtil::getParam('sid');
        require_once CODE_BASE . '/app/stock/model/StockCompanyNamespace.class.php';
        $companyList = StockCompanyNamespace::getCompanyMarketInfo(array($sid));
        if (empty($companyList[$sid])) {
            $this->output(400, '', '上市公司不存在');
        }
        $price = HttpUtil::getParam('price');
        $tmpPrice = $price == -1 ? $companyList[$sid]['price'] : $price;
        $count = HttpUtil::getParam('count');

        //委买，检查用户金额
        if ($direction == 1 && $count * $tmpPrice * (1 + 0.001) >= $user['available_money']) {
            $this->output(400, '', '可用资金不足');
        }
        //添加委托数据
        $dData = array(
            'uid' => $user['uid'],
            'sid' => $sid,
            'count' => $count,
            'price' => $price,
            'direction' => $direction,
            'status' => 0,
            'time' => time(),
        );
        $addFlag = DelegateListModel::addDelegate($dData);
        if ($addFlag) {
            $this->output(200, '', 'OK');
        } else {
            $this->output(500, '', '保存数据失败');
        }
    }

    public function cancelDelegateAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请刷新页面，重新登录');
        }

        $dlgId = HttpUtil::getParam('dlgid');
        $delegateInfo = DelegateListModel::getDelegateInfo($dlgId);
        if (empty($delegateInfo) || $delegateInfo[0]['uid'] != $user['uid']) {
            $this->output(400, '', '无权限操作');
        }
        $update = array('status' => DelegateListModel::$STATUS_VALUE['cancel'], 'update_t' => time());
        $opFlag = DelegateListModel::updateDelegate($dlgId, $update);
        if ($opFlag) {
            $this->output(200, '', '操作成功');
        } else {
            $this->output(500, '', '操作失败');
        }
    }
}
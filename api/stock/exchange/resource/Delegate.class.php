<?php
/**
 * 用户交易委托
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/16
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/model/DelegateListModel.class.php';
require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
require_once CODE_BASE . '/app/user/StockUserNamespace.class.php';
require_once CODE_BASE . '/app/stock/model/UserStockModel.class.php';

class DelegateRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/delegate/' => array(
                'GET' => 'getDelegateList',         //获取用户委托列表
                'POST' => 'createDelegate',         //提交委托记录
            ),
            '/delegate/:dlgid/' => array(
                'DELETE' => 'cancelDelegate',       //撤销委托
            ),
        );
    }

    public function getDelegateListAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请刷新页面，重新登录');
        }
        $direction = HttpUtil::getParam('direction');
        if (!in_array($direction, [1, -1])) {
            $this->output(400, '', '委托买卖方向数据错误');
        }
        $deleagteList = array();
        //查询有效委托列表
        $list = DelegateListModel::getUserDlgList($user['uid'], $direction);
        if (!empty($list)) {
            $sidArr = array();
            foreach ($list as $item) {
                $sidArr[] = $item['sid'];
            }
            //查询公司名称
            $companyList = StockCompanyNamespace::getCompanyMarketInfo($sidArr);
            foreach ($list as $item) {
                $deleagteList[] = array(
                    'did' => $item['id'],
                    'sid' => $item['sid'],
                    'sname' => !empty($companyList[$item['sid']]) ? $companyList[$item['sid']]['sname'] : $item['sid'],
                    'price' => StockCompanyNamespace::showPrice($item['price']),
                    'count' => $item['count'],
                    'time' => $item['time'],
                );
            }
        }
        $this->output(200, $deleagteList);
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
        $companyList = StockCompanyNamespace::getCompanyMarketInfo(array($sid));
        if (empty($companyList[$sid])) {
            $this->output(400, '', '上市公司不存在');
        }
        $price = HttpUtil::getParam('price');
        $tmpPrice = $price == -1 ? $companyList[$sid]['price'] : $price;
        $count = HttpUtil::getParam('count');
        if ($count % 100 != 0) {
            $this->output(400, '', '股份数量需为100股整数倍');
        }

        //委卖
        $freezeMoney = 0;
        if (-1 == $direction) {
            //委卖检查用户可用股票数
            $stockArr = UserStockModel::selectStockBySid($user['uid'], $sid);
            if ($stockArr[0]['available_count'] < $count) {
                $this->output(400, '', '可用股份不足');
            }
        } else {
            //委买，检查用户金额
            $freezeMoney = $count * $tmpPrice * (1 + 0.001);    //当前委买需要支付的冻结金额
            if ($freezeMoney >= $user['usable_money']) {
                $this->output(400, '', '可用资金不足');
            }
        }
        
        //添加委托数据
        $dData = array(
            'uid' => $user['uid'],
            'sid' => $sid,
            'count' => $count,
            'price' => $price,
            'freeze_money' => $freezeMoney,
            'direction' => $direction,
            'status' => 0,
            'time' => time(),
        );
        $addFlag = DelegateListModel::addDelegate($dData);
        if ($addFlag) {
            if (-1 == $direction) {
                //委卖暂扣可卖股票
                UserStockModel::updateUserStock($user['uid'], $sid, array(
                    'available_count' => $stockArr[0]['available_count'] - $count,
                ));
            } else {
                //委买暂扣用户可用金额
                StockUserNamespace::setUserInfo($user['uid'], [
                    'usable_money' => $user['usable_money'] - $freezeMoney,
                ]);
            }
            $this->output(200, '', '委托成功');
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
        $delegateItem = $delegateInfo[0];
        $update = array('status' => DelegateListModel::$STATUS_VALUE['cancel'], 'update_t' => time());
        $opFlag = DelegateListModel::updateDelegate($dlgId, $update);
        if ($opFlag) {
            if (-1 == $delegateItem['direction']) {
                //原委卖恢复冻结股票
                $stockArr = UserStockModel::selectStockBySid($user['uid'], $delegateItem['sid']);
                UserStockModel::updateUserStock($user['uid'], $delegateItem['sid'], array(
                    'available_count' => $stockArr[0]['available_count'] + $delegateItem['count'],
                ));
            } else {
                //委买恢复冻结的可用金额
                $stockUserInfo = StockUserNamespace::getUserInfoById($user['uid']);
                StockUserNamespace::setUserInfo($user['uid'], [
                    'usable_money' => $stockUserInfo['usable_money'] + $delegateItem['freeze_money'],
                ]);
            }
            $this->output(200, '', '撤销成功');
        } else {
            $this->output(500, '', '撤销失败');
        }
    }
}
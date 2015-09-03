<?php
/**
 * 用户股票接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/03
 * @copyright nebula-fund.com
 */

class StockRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/stock/' => array(
            ),
            '/stock/:sid/losslimit/' => array(
                'PUT' => 'setStockLosslimit',       //设置止损
            ),
        );
    }

    public function setStockLosslimitAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请重新登录');
        }

        $sid = HttpUtil::getParam('sid');
        $price = HttpUtil::getParam('price');
        if (!is_numeric($sid) || $price <= 0) {
            $this->output(400, '', '请求参数错误');
        }
        require_once CODE_BASE . '/app/stock/model/UserStockModel.class.php';
        //先获取是否已经持股，有的话个更新
        $userStock = UserStockModel::selectStockBySid($user['uid'], $sid);
        if (!empty($userStock)) {
            $update = array('loss_limit' => $price);
            $opFlag = UserStockModel::updateUserStock($user['uid'], $sid, $update);
        } else {
            $data = array(
                'uid' => $user['uid'],
                'sid' => $sid,
                'loss_limit' => $price,
            );
            $opFlag = UserStockModel::addUserStock($data);
        }
        if ($opFlag) {
            $this->output(200, '', '操作成功');
        } else {
            $this->output(500, '', '保存失败');
        }
    }
}

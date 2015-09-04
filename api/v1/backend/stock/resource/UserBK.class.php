<?php
/**
 * 后台使用的股票用户获取接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/04
 * @copyright nebula-fund.com
 */

class UserBKRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/userbk/info/:stockuid/' => array(
                'GET' => 'getStockUserInfo',        //获取股票用户资料
            ),
        );
    }

    public function getStockUserInfoAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $uid = HttpUtil::getParam('stockuid');
        require_once CODE_BASE . '/app/user/StockUserNamespace.class.php';
        $userInfo = StockUserNamespace::getUserInfoById($uid);
        if (empty($userInfo)) {
            $this->output(404, '', '获取数据为空');
        } else {
            $this->output(200, $userInfo);
        }
    }
}

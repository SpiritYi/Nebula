<?php
/**
 * Stock 用户账户相关接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/22
 * @copyright nebula-fund.com
 */

class SessionRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/session/' => array(
                'POST' => 'createSessionLogin',     //登录创建session
            ),
        );
    }

    public function createSessionLoginAction() {
        $username = HttpUtil::getParam('username');
        if (empty($username)) {
            ResourceBase::output(400, '', '用户名不能为空');
        }
        $pwd = HttpUtil::getParam('token');
        if (empty($pwd)) {
            ResourceBase::output(400, '', '密码不能为空');
        }
        require_once CODE_BASE . '/app/user/model/StockUserInfoModel.class.php';
        $userRes = StockUserInfoModel::selectUserPwd($username);
        $userInfo = $userRes[0];
        if (empty($userInfo)) {
            ResourceBase::output(400, '', '认证用户不存在');
        }
        if (!password_verify($pwd, $userInfo['password'])) {
            ResourceBase::output(400, '', '用户名或密码错误');
        }
        //创建cookie
        $verifyStr = sprintf('%s_%d', $userInfo['uid'], time() + $userInfo['session_expire']);
        require_once CODE_BASE . '/util/http/CookieUtil.class.php';
        require_once CODE_BASE . '/app/user/StockUserNamespace.class.php';
        $cookie = CookieUtil::create(StockUserNamespace::USER_VERIFY_COOKIE_KEY, $verifyStr, $userInfo['session_expire']);
        //更新活跃时间
        $update = array('id' => $userInfo['uid'], 'live_time' => time());
        StockUserNamespace::setUserInfo($userInfo['uid'], $update);
        ResourceBase::output(200, array('cookie' => $cookie), 'OK');
    }
}

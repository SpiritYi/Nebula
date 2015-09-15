<?php
/**
 * 管理用户session
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/27
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/user/UserNamespace.class.php';

class SessionRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/session/' => array(
                'POST' => 'createSessionLogin',     //登录
                'DELETE' => 'signout',              //注销
            ),
        );
    }

    public function createSessionLoginAction() {
        $username = HttpUtil::getParam('username');
        if (empty($username)) {
            ResourceBase::output(400, '', '数据为空');
        }
        $userInfo = UserNamespace::getUserInfo($username);
        if (empty($userInfo)) {
            ResourceBase::output(404, '', '认证用户不存在');
        }
        $adminType = HttpUtil::getParam('admin_type');
        if (!empty($adminType)) {   //管理员登陆校验
            require_once CODE_BASE . '/app/user/AdminUserNamespace.class.php';
            if ($userInfo['admin_type'] != AdminUserNamespace::TYPE_ADMIN) {
                $this->output(403, '', '认证用户权限不足');
            }
        }
        //创建cookie
        $verifyStr = sprintf('%s_%d', $username, time() + $userInfo['session_expire']);
        require_once CODE_BASE . '/util/http/CookieUtil.class.php';
        $cookie = CookieUtil::create(UserNamespace::USER_VERIFY_COOKIE_KEY, $verifyStr, $userInfo['session_expire']);
        //更新活跃时间
        $update = array('id' => $userInfo['id'], 'active_time' => time());
        UserNamespace::setUserInfo($userInfo['id'], $update);
        ResourceBase::output(200, array('cookie' => $cookie), 'OK');
    }

    public function signoutAction() {
        $verify = HttpUtil::getParam('verify_user');
        /*
        $cookieData = UserNamespace::splitVerifyCookie($verify);
        if (empty($cookieData)) {
            Logger::logError('解密用户数据失败', 'signout');
        }
        */
        ResourceBase::output(200, '', 'OK');

    }
}

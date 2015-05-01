<?php
/**
 * 用户资料接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/05
 * @copyright nebula-fund.com
 */

class ProfileRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/profile/:uid/' => array(
                'PUT' => 'modifyProfile',   //修改用户资料
            ),
        );
    }

    public function modifyProfileAction() {
        $user = $this->getSessionUser();
        if (empty($user))
            $this->output(403, '', '请重新登录');

        $modify = array(
            'nickname' => HttpUtil::getParam('nickname'),
            'email' => HttpUtil::getParam('email'),
            'phone' => HttpUtil::getParam('phone'),
        );
        require_once CODE_BASE . '/app/user/UserNamespace.class.php';
        $res = UserNamespace::setUserInfo($user['id'], $modify);
        if ($res) {
            $this->output(200, '', '更新成功');
        } else {
            $this->output(500, '', '服务器出错，请稍后重试');
        }
    }
}

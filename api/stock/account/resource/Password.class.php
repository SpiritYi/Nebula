<?php
/**
 * 用户密码操作接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/06
 * @copyright nebula-fund.com
 */

class PasswordRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/password/' => array(
                'PUT' => 'changePassword',      //用户修改密码
            ),
        );
    }

    public function changePasswordAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请重新登录');
        }
        $pwdOrigin = HttpUtil::getParam('pwd_origin');
        $newPwd = HttpUtil::getParam('new_pwd');
        $newPwdAgain = HttpUtil::getParam('new_pwd_again');
        if ($newPwd != $newPwdAgain) {
            $this->output(400, '', '两次新密码不相同');
        }

        //校验原密码
        require_once CODE_BASE . '/app/user/model/StockUserInfoModel.class.php';
        $userPwd = StockUserInfoModel::selectUserPwd($user['username']);
        if (!password_verify($pwdOrigin, $userPwd[0]['password'])) {
            $this->output(400, '', '原密码错误');
        }
        //生成并更新新密码hash
        $pwdHash = password_hash($newPwd, PASSWORD_BCRYPT);
        require_once CODE_BASE . '/app/user/StockUserNamespace.class.php';
        $res = StockUserNamespace::setUserInfo($user['uid'], array('password' => $pwdHash, 'active_time' => time()));
        if ($res) {
            $this->output(200, '', '修改密码成功, 请重新登录');
        } else {
            $this->output(500, '', '修改密码失败');
        }

    }
}

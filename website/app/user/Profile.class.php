<?php
/**
 * 用户资料页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/04
 * @copyright nebule-fund.com
 */

require_once WEBSITE . '/app/user/UserMaster.class.php';

class ProfilePage extends UserMaster {
    public function loadHead() {
        $this->staExport('<title>我的资料</title>');
    }

    public function userAction() {
        $this->myInfo = $this->getSessionUser();
        $this->render('/user/profile.php');
    }

    //填充字符串输出
    public function strFormat($origin, $length) {
        return $origin . str_repeat('&emsp;', $length - strlen($origin));
    }
}

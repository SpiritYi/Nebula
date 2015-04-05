<?php
/**
 * 用户中心基类页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/04
 * @copyright nebula-fund.com
 */

abstract class UserMaster extends Master {
    public function __construct() {
        parent::__construct();
    }

    public function action() {
        $this->render('user/user_master.php');
    }

    abstract function userAction();
}

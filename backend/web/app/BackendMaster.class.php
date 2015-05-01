<?php
/**
 * 后台主模板页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

abstract class BackendMaster extends PageBase {

    public function __construct() {
        require_once CODE_BASE . '/app/user/AdminUserNamespace.class.php';
        //页面请求均需验证
        $this->userInfo = $this->accessVerify();
        if (empty($this->userInfo) || $this->userInfo['admin_type'] != AdminUserNamespace::TYPE_ADMIN) {
            $originUri = $_SERVER['REQUEST_URI'];
            if (empty($originUri)) {
                $originUri = '/';
            }
            header('location:/passport?loc=' . urlencode($originUri));     //跳转到认证页面, 附带原先连接
        }

        PageBase::render('/backend_master.php');
    }

    abstract public function loadHead();

    abstract public function action();
}

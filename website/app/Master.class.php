<?php
/**
 * 所有页面主模板页
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2013/04/03
 */

abstract class Master extends PageBase {

    public function __construct() {
        //页面请求均需验证
        $this->userInfo = $this->accessVerify();
        if (empty($this->userInfo)) {
            $originUri = $_SERVER['REQUEST_URI'];
            if (empty($originUri)) {
                $originUri = '/';
            }
            header('location:/verify?loc=' . urlencode($originUri));     //跳转到认证页面, 附带原先连接
        }

        PageBase::render('master.php');
    }

    abstract public function loadHead();

    abstract public function action();
}

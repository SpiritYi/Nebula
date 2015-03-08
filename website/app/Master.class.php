<?php
/**
 * 所有页面主模板页
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2013/04/03
 */

abstract class Master extends PageBase {

    public function __construct() {
        //页面请求均需验证
        header('location:/verify');

        PageBase::render('master.php');
    }

    abstract public function loadHead();

    abstract public function action();
}

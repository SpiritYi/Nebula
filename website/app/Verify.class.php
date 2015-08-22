<?php
/**
 * 验证身份页面
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/08
 * @copyright nebula.com
 */

class VerifyPage extends EmptyPage {

    public function loadHead() {
        $this->staExport('<title>星云财富基金</title>');
        $this->staExport('/css/Verify.css');
    }

    public function action() {
        $this->userInfo = $this->accessVerify();
        if (!empty($this->userInfo)) {
            require_once CODE_BASE . '/util/http/HttpUtil.class.php';
            $loc = HttpUtil::getParam('loc');
            $loc = empty($loc) ? '/' : $loc;
            header('location:' . $loc);
        }

        $this->render('verify.php');
    }
}

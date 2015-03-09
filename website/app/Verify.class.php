<?php
/**
 * 验证身份页面
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/08
 * @copyright nebula.com
 */

class VerifyPage extends EmptyPage {

    public function loadHead() {
        $this->headExport('<title>Nebula Verification</title>');
        $this->headExport('/css/Verify.css');
    }

    public function action() {
        require_once CODE_BASE . '/util/http/HttpUtil.class.php';
        $this->render('verify.php');

        $role = HttpUtil::getParam('role');
        if ($role == 'ceo') {   //测试阶段指定参数种cookie
            require_once CODE_BASE . '/util/http/CookieUtil.class.php';
            CookieUtil::write(PageBase::VERIFY_USER_KEY, 'admin.test_' . strtotime('+7 day'), 7 * 24 * 3600);
            $loc = HttpUtil::getParam('loc');
            header('location:' . $loc);
        }
        // CookieUtil::delete(self::VERIFY_USER_KEY);
    }
}

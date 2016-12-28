<?php
/**
 * 管理后台通过认证页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

require_once LEO_BACKEND_WEB . '/app/LeoBackendEmpty.class.php';

class LeoPassportPage extends LeoBackendEmpty {

    public function loadHead() {
        $this->staExport('<title>Nebula 后台认证</title>');
    }

    public function action() {
        require_once CODE_BASE . '/app/user/AdminUserNamespace.class.php';
        $this->userInfo = $this->accessVerify();
        if (!empty($this->userInfo) && $this->userInfo['admin_type'] == AdminUserNamespace::TYPE_ADMIN) {
            require_once CODE_BASE . '/util/http/HttpUtil.class.php';
            $loc = HttpUtil::getParam('loc');
            $loc = empty($loc) ? '/' : $loc;
            header('location:' . $loc);
        }

        $this->render('/leo_passport.php');
    }
}

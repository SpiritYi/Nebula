<?php
/**
 * 用户配置数据配置入口页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

class UserShipPage extends BackendMaster {
    public function loadHead() {
        $this->staExport('<title>用户数据管理</title>');
    }

    public function action() {
        require_once BACKEND_WEB . '/model/user/UserInfoBKModel.class.php';
        $this->allUserList = UserInfoBKModel::getAllUser();
        require_once CODE_BASE . '/model/user/PropertyRecordModel.class.php';
        $this->propertyType = PropertyRecordModel::$TYPE_NAME;
        $this->render('/ship/user/user_ship.php');
    }
}

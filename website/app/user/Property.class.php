<?php
/**
 * 用户资产统计页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/04
 * @copyright nebula-fund.com
 */

require_once WEBSITE . '/app/user/UserMaster.class.php';

class PropertyPage extends UserMaster {

    public function loadHead() {
        $this->staExport('<title>我的资产</title>');
    }

    public function userAction() {
        require_once WEBSITE . '/model/user/PropertyRecordModel.class.php';
        $userInfo = $this->getSessionUser();
        //展示用户总资产
        $this->userPropertyCount = PropertyRecordModel::getCountProperty($userInfo['id']);

        $this->recordList = PropertyRecordModel::getRecordList($userInfo['id'], 0, 20);
        $this->render('user/property.php');
    }
}

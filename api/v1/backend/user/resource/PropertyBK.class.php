<?php
/**
 * 管理后台用户操作接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

class PropertyBKRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/propertybk/:userid/' => array(
                'POST' => 'addUserPropertyRecord',    //添加用户资产记录
            ),
        );
    }

    public function addUserPropertyRecordAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }

        $uid =      HttpUtil::getParam('userid');
        $type =     HttpUtil::getParam('type');
        $amount =   HttpUtil::getParam('amount');
        $notes =    HttpUtil::getParam('notes');
        $timeStr =     HttpUtil::getParam('time');
        $time = strtotime($timeStr);

        if (empty($uid) || empty($type) || empty($notes) || $time <= 0) {
            $this->output(400, '', '请把数据填写完整');
        }

        $data = array(
            'user_id' => $uid,
            'type' => $type,
            'amount' => $amount,
            'notes' => $notes,
            'time' => $time,
        );
        require_once BACKEND . '/web/model/user/PropertyRecordBKModel.class.php';
        $flag = PropertyRecordBKModel::addRecord($data);
        if ($flag) {
            $this->output(200, $data, '操作成功');
        } else {
            $this->output(500, $data, '保存失败');
        }
    }
}

<?php
/**
 * 赶集叮咚解耦
 * @author zhaozhiqiang
 * @version 2014/12/19
 * @copyright ganji.com
 */
require_once CODE_BASE2 . '/app/msactive/DingDongNamespace.class.php';
require_once CODE_BASE2 . '/interface/uc/UserInterface.class.php';

class DingDong extends ResourceBase {

    private static $_USER = array();

    public function setUriMatchConfig() {
        return array(
            '/dingdong/qunzu/' => array(
                'GET' => 'getData',      //获取点赞人和
            ),
            '/dingdong/qunzu/zan/' => array(
                'POST' => 'increase',      //增加计数
            ),
        );
    }

    //check login
    private function _authUser($userId = 0) {
        $token = clientPara::getArg('token');
        if(!$userId) return false;

        $flag = clientPara::auth_check($userId, $token);
        if ($flag) {
            self::$_USER = UserInterface::getUser($userId);
        }
    }

    //get counter and list
    public function getDataAction() {
        $list    = DingDongNamespace::getLatestData();
        $counter = DingDongNamespace::getCounter();
        $res     = ResourceBase::formatReturn(200, array('list' => $list, 'counter' => $counter), '');
        ResourceBase::display($res);exit;
    }

    //increase the counter
    public function increaseAction() {
        $userId = clientPara::getArg('user_id');
        $this->_authUser($userId);
        DingDongNamespace::updateCounter();

        //登录用户记录
        if(self::$_USER) {
            $data['user_id']  = self::$_USER['user_id'];
            $data['username'] = self::$_USER['username'];
            $data['created']  = time();
            DingDongNamespace::saveData($data);
        }
        $res  = ResourceBase::formatReturn(200, 1, '');
        ResourceBase::display($res);exit;
    }
}
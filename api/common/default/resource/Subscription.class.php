<?php
/**
 * h5 push resource. 收集，取消用户推送
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2014/11/20
 * @copyright ganji.com
 */

class Subscription extends ResourceBase {

    //配置路由对应调用方法
    public function setUriMatchConfig() {
        return array(
            '/subscription/' => array(
                'GET' => 'show',
                'POST' => 'create',
                'DELETE' => 'remove',
            ),
        );
    }

    //获取用户开关设置
    public function showAction() {
        $installId = (int)clientPara::getArg('install_id');
        // $customerId = clientPara::getArg('customer_id');
        $userType = (int)clientPara::getArg('user_type');

        require_once CLIENT_API . '/common/default/model/ClientHfPushUser.class.php';
        $userSet = ClientHfPushUser::getUserPush($installId, $userType);
        if (empty($userSet)) {
            $data = array('set' => 0);
        } else {
            $data = array(
                'set' => 1,
                'id' => sprintf('%s_%s', $userSet[0]['install_id'], $userSet[0]['user_type']),
            );
        }

        $res = ResourceBase::formatReturn(200, $data, '');
        ResourceBase::display($res);
    }

    //添加对一个活动推送收取
    public function createAction() {
        $param = array(
            'install_id' => (int)clientPara::getArg('install_id'),
            'user_type' => (int)clientPara::getArg('user_type'),
            'customer_id' => clientPara::getArg('customerId'),
        );
        if ($param['customer_id'] == 1001 || $param['install_id'] < 10000) {
            Logger::logError(var_export(clientPara::$param['post'], true), 'Subscription');
        }

        foreach ($param as $key => $val) {
            if ($val <= 0) {
                $res = ResourceBase::formatReturn(400, '', $key . ' unqualifield.', -1);
                ResourceBase::display($res);exit;
            }
        }
        require_once CLIENT_API . '/common/default/model/ClientHfPushUser.class.php';
        $result = ClientHfPushUser::addPushUser($param);
        if ($result) {
            $data = array(
                'id' => sprintf('%s_%s', $param['install_id'], $param['user_type']),
                'install_id' => $param['install_id'],
                'user_type' => $param['user_type'],
                'customer_id' => $param['customer_id'],
            );
            $res = ResourceBase::formatReturn(201, $data, 'created');
            ResourceBase::display($res);exit;
        } else {
            $res = ResourceBase::formatReturn(500, '', 'Insert error', -1);
            ResourceBase::display($res);exit;
        }
    }

    //取消一个活动的订阅
    public function removeAction() {
        $id = clientPara::getArg('id');     //删除参数id, {$installId}_{$userType}
        list($installId, $userType) = explode('_', $id);
        if ($installId <= 0 || $userType <= 0) {
            $res = ResourceBase::formatReturn(400, '', 'param error', -1);
            ResourceBase::display($res);exit;
        }

        require_once CLIENT_API . '/common/default/model/ClientHfPushUser.class.php';
        $flag = ClientHfPushUser::cancelPush($installId, $userType);
        if ($flag) {
            $res = ResourceBase::formatReturn(200, '', 'push cancel');
            ResourceBase::display($res);exit;
        } else {
            $res = ResourceBase::formatReturn(500, '', 'delete data failed', -1);
            ResourceBase::display($res);exit;
        }
    }
}

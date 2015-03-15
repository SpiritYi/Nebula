<?php
/**
 * 积分任务task
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2014/12/13
 * @copyright ganji.com
 */

require_once CODE_BASE2 . '/app/mobile_client/ClientUserCreditNamespace.class.php';

class CreditTasks extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/credittasks/' => array(
                'GET' => 'getTaskList',        //获取任务列表
            ),
            '/credittasks/:id/' => array(
                'GET' => 'getTaskDetail',      //获取任务详情
            ),
        );
    }

    //校验当前请求的用户
    private function _authUser($userId = 0) {
        $token = clientPara::getArg('token');
        if (empty($userId)) {
            $userId = $this->URI_DATA['id'];
        }

        $flag = clientPara::auth_check($userId, $token);
        if (!$flag) {
            $res = ResourceBase::formatReturn(403, '', 'user authentication failed.', -1);
            ResourceBase::display($res);exit;
        }
    }

    //获取任务列表
    public function getTaskListAction() {
        $loginId = clientPara::getArg('login_id');
        if (empty($loginId) || !is_numeric($loginId)) {  //非登录用户返回原始列表
            $taskList = ClientUserCreditNamespace::getTaskList();
            unset($taskList[100]);
            unset($taskList[101]);
            $res = ResourceBase::formatReturn(200, array_values($taskList), '');
            ResourceBase::display($res);exit;
        } else {
            $this->_authUser($loginId);

            $taskList = ClientUserCreditNamespace::getUserTaskList($loginId);
            $res = ResourceBase::formatReturn(200, array_values($taskList), '');
            ResourceBase::display($res);exit;
        }
    }

    //获取积分任务详情
    public function getTaskDetailAction() {
        require_once CODE_BASE2 . '/app/mobile_client/model/credit/CreditTasksModel.class.php';
        $taskInfo = CreditTasksModel::getTaskById($this->URI_DATA['id']);
        if (empty($taskInfo)) {
            $res = ResourceBase::formatReturn(404, '', 'task not found.', -1);
            ResourceBase::display($res);exit;
        }
        $res = ResourceBase::formatReturn(200, $taskInfo[0], '');
        ResourceBase::display($res);exit;
    }
}

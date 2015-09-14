<?php
/**
 * 用户消息接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/06
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/model/StockMessageModel.class.php';

class MessageRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/message/' => array(
                'GET' => 'getMsgList',      //获取消息列表
            ),
            '/message/:mid/' => array(
                'PUT' => 'readMsg',         //阅读一条消息
            ),
        );
    }

    public function getMsgListAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请刷新页面，重新登录', 40301);
        }

        $classify = HttpUtil::getParam('classify');
        switch ($classify) {
            case 'unread':      //获取未读消息
                $mids = HttpUtil::getParam('mids');
                $clientIds = [];
                if (!empty($mids)) {
                    $clientIds = array_flip(explode(',', $mids));   //反转key <=> value
                }

                $newList = array();
                $msgList = StockMessageModel::getUnreadList($user['uid']);
                if (!empty($msgList)) {
                    foreach ($msgList as $msgItem) {
                        if (array_key_exists($msgItem['id'], $clientIds)) {
                            unset($clientIds[$msgItem['id']]);  //过滤未读的，剩下已读
                        } else {    //新的消息
                            $newList[] = $msgItem;
                        }
                    }
                }
                //如果消息多，再获取未读消息总量
                $unreadCount = count($msgList);
                if ($unreadCount >= 10) {
                    $total = StockMessageModel::getUnreadCount($user['uid']);
                    if (!empty($total)) {
                        $unreadCount = $total[0]['total'];
                    }
                }
                $this->output(200, array('read_ids' => array_keys($clientIds), 'new_list' => $newList, 'unread_count' => $unreadCount));
                break;
        }
        $this->output(400, '', '请求参数错误');
    }

    public function readMsgAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请刷新页面，重新登录');
        }

        $mid = HttpUtil::getParam('mid');
        $flag = StockMessageModel::readMsg($user['uid'], $mid);
        if ($flag) {
            $this->output(200, '', 'OK');
        } else {
            $this->output(500, '', '操作失败');
        }
    }
}

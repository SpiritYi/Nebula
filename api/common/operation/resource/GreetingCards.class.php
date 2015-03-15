<?php
/**
 * 春节贺卡活动接口
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2015/01/21
 * @copyright ganji.com
 */


require_once CLIENT_API . '/common/operation/model/GreetingCardsModel.class.php';

class GreetingCards extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/greetingcards/' => array(
                'POST' => 'createGreetingCards',    //创建贺卡
            ),
            '/greetingcards/:card_id/' => array(
                'GET' => 'getCardInfo',             //获取贺卡信息
            ),
        );
    }

    public function setParamConfig() {
        return array(
            'createGreetingCards' => array(
                'card_info' => 'string',
            ),
            'getCardInfo' => array(
                'card_id' => 'int',
            ),
        );
    }

    public function checkParam($paramKey) {
        switch ($paramKey) {
            case 'card_info':
                if (empty($this->_param[$paramKey]) || !is_array(json_decode($this->_param[$paramKey], true))) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_PARAM, '', $paramKey . " not valid json.");
                }
            case 'card_id':
                if (empty($this->_param[$paramKey])) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_NOT_FOUND);
                }
                break;
        }
    }

    /**
     * POST /api/common/operation/greetingcards/
     */
    public function createGreetingCardsAction() {
        $data = array(
            'install_id' => clientPara::getArg('installId'),
            'content' => $this->_param['card_info'],
            'create_time' => time(),
        );
        $id = GreetingCardsModel::createGreetingCard($data);
        if (empty($id)) {
            Logger::logError('create greeting cards failed. ' . var_export($data, true), 'GreetingCards');
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SYSTEM, '', 'insert data failed.');
            ResourceBase::display($res);return;
        }
        $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS, '', '', $id);
        ResourceBase::display($res);return;
    }

    /**
     * GET /api/common/operation/greetingcards/3135/
     */
    public function getCardInfoAction() {
        $res = GreetingCardsModel::getGreetingCard($this->_param['card_id']);
        if (empty($res)) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_NOT_FOUND, '', 'No select result.');
            ResourceBase::display($res);return;
        }
        $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS, '', '', json_decode($res[0]['content']));
        ResourceBase::display($res);return;
    }
}

<?php
/**
 * 用户意见建议接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/18
 * @copyright nebula-fund.com
 */

class SupportRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/support/suggestion/' => array(
                'POST' => 'addSuggestion',      //保存用户建议
            ),
        );
    }

    public function addSuggestionAction() {
        $user = $this->getSessionUser();
        if (empty($user))
            $this->output(403, '', '请重新登录');

        $content = HttpUtil::getParam('content');
        $contentLen = mb_strlen($content, 'UTF8');
        if ($contentLen > 1000) {
            $this->output(400, '', '内容长度已超过1000。当前长度:' . $contentLen);
        }
        $data = array(
            'user_id' => $user['id'],
            'content' => htmlspecialchars($content),
            'time' => time(),
        );
        require_once API . '/v1/company/model/SuggestionModel.class.php';
        if (SuggestionModel::saveSuggestion($data)) {
            $this->output(200, '提交成功', 'OK');
        } else {
            $this->output(500, '', '服务错误，请稍后重试');
        }
    }
}

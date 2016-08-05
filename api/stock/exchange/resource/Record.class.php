<?php
/**
 * 用户委托记录接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/06/10
 * @copyright nebula_fund.com
 */

class RecordRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/record/' => array(
                'GET' => 'getUserExchangeListAction',       //获取用户的
            ),
        );
    }

    public function getUserExchangeListAction() {

    }
}

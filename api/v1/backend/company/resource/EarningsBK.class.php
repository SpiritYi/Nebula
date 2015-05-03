<?php
/**
 * 公司营收数据接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/03
 * @copyright nebula-fund.com
 */


class EarningsBKRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/earningsbk/' => array(
                'POST' => 'addEarningsRate',        //添加营收数据
            ),
        );
    }

    //添加营收数据接口
    public function addEarningsRateAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $type = HttpUtil::getParam('type');
        $rate = HttpUtil::getParam('rate');
        $timeStr = HttpUtil::getParam('time');
        $dateM = strtotime($timeStr);

        require_once API . '/v1/company/model/EarningsRateModel.class.php';
        if (!array_key_exists($type, EarningsRateModel::$TYPE_NAME) || $dateM <= 0) {
            $this->output(400, '', '数据填写不正确');
        }
        if (!(-100 < $rate && $rate < 100)) {
            $this->output(400, '', '百分比数值不在正确范围');
        }

        $data = array(
            'type' => $type,
            'rate' => number_format($rate, 2, '.', ''),
            'date_m' => $dateM,
        );
        require_once BACKEND . '/web/model/company/EarningsRateBKModel.class.php';
        $res = EarningsRateBKModel::addRateRecord($data);
        if ($res) {
            $this->output(200, $res, '提交成功');
        } else {
            $this->output(500, false, '保存数据失败');
        }
    }
}

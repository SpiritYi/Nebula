<?php
/**
 * 外汇总值数据接口，backend 使用
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/09/30
 * @copyright nebula-fund.com
 */

require_once BACKEND . '/web/model/stock/ForexAssetBKModel.class.php';

class ForexAssetBKRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            'forexassetbk' => array(
                'GET' => 'queryForexAsset',     //查询资产数据
                'POST' => 'saveForexAsset',     //保存外汇总值
            ),
        );
    }

    public function queryForexAssetAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $category = HttpUtil::getParam('cat');      //查询操作的数据类别
        switch ($category) {
            case 'latest_asset':
                $record = ForexAssetBKModel::getLatestRecord();
                if (empty($record)) {
                    $this->output(200, ['asset' => '']);
                } else {
                    $this->output(200, ['asset' => sprintf('%.2f', $record[0]['asset'])]);
                }
                break;
            
            default:
                $this->output(400, '', '无效的操作类别');
                break;
        }
    }

    /**
     * 保存资产总值记录
     *  - asset float       //总值
     *  - date_str string   //日期字符串
     */
    public function saveForexAssetAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $asset = HttpUtil::getParam('asset');
        $dateStr = HttpUtil::getParam('date_str');
        $dateTime = strtotime($dateStr);
        if ($dateTime < strtotime('-10 day') || strtotime('+10 day') < $dateTime) {
            $this->output(400, '', '日期时间超过10天');
        }
        $record = array(
            'asset' => $asset,
            'time' => $dateTime,
        );
        $res = ForexAssetBKModel::addRecord($record);
        if (!$res) {
            $this->output(500, '', '服务器保存失败');
        }
        $this->output(200, '', '操作成功');
    }
}
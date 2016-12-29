<?php
/**
 * 物品后台接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/29
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/leo/cargo/LeoCargoNamespace.class.php';

class LeoCargoBKRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/leocargobk/' => array(
                'POST' => 'addRecord',      //添加数据
            ),
            '/leocargobk/:id/' => array(
                'PUT' => 'updateRecord',
                'DELETE' => 'deleteRecord',
            ),
        );
    }

    public function addRecordAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $name = HttpUtilEx::getParam('name');
        if (empty($name)) {
            $this->output(400, [], '名称不能为空');
        }
        $price = HttpUtilEx::getParam('price');
        if ($price <= 0) {
            $this->output(400, [], '价格不正确');
        }
        $content = HttpUtilEx::getParam('content');
        $website = HttpUtilEx::getParam('desc_website');
        $companyId = HttpUtilEx::getParam('company_id');
        require_once CODE_BASE . '/app/leo/company/LeoCompanyNamespace.class.php';
        $cmpInfo = LeoCompanyNamespace::getBatchInfo([$companyId]);
        if (empty($cmpInfo[$companyId])) {
            $this->output(400, [], '公司不存在');
        }
        $data = array(
            'name' => $name,
            'price' => $price,
            'content' => $content,
            'desc_website' => $website,
            'company_id' => $companyId,
            'status' => 0,
        );
        $res = LeoCargoNamespace::addRecord($data);
        if ($res) {
            $this->output(200, [], '添加成功');
        } else {
            $this->output(500, [], '保存失败');
        }
    }

    //更新数据
    public function updateRecordAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $id = HttpUtilEx::getParam('id');
        $infoArr = LeoCargoNamespace::getBatchInfo([$id]);
        if (empty($infoArr[$id])) {
            $this->output(400, '', '物品不存在');
        }
        $cargoInfo = $infoArr[$id];

        $fieldConfig = array('name', 'price', 'content', 'desc_website', 'company_id');
        //匿名函数处理改动的数据
        $fieldCheck = function($fieldConfig, $info) {
            $change = array();
            foreach ($fieldConfig as $cItem) {      //同样的校验方式检测每个参数
                $postD = HttpUtilEx::getParam($cItem, -1);
                if ($postD != -1 && $postD != $info[$cItem]) {      //有提交并且不同于数据库的数据
                    $change[$cItem] = $postD;
                }
            }
            return $change;
        };
        $updateData = $fieldCheck($fieldConfig, $cargoInfo);

        if (isset($updateData['price']) && $updateData['price'] <= 0) {
            $this->output(400, [], '价格不正确');
        }
        if (empty($updateData)) {
            $this->output(400, [], '数据未变动');
        }

        $res = LeoCargoNamespace::updateRecord($id, $updateData);
        if ($res) {
            $this->output(200, [], '操作成功');
        } else {
            $this->output(500, [], '更新失败');
        }
    }

    public function deleteRecordAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $id = HttpUtilEx::getParam('id');
        $infoArr = LeoCargoNamespace::getBatchInfo([$id]);
        if (empty($infoArr[$id])) {
            $this->output(400, '', '物品不存在');
        }
        $data = array('status' => -1);
        $res = LeoCargoNamespace::updateRecord($id, $data);
        if ($res) {
            $this->output(200, [], '操作成功');
        } else {
            $this->output(500, [], '删除失败');
        }
    }

}
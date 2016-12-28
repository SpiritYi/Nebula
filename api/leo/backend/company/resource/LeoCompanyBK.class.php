<?php
/**
 * leo 供应商管理后台接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/28
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/leo/company/LeoCompanyNamespace.class.php';

class LeoCompanyBKRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/leocompanybk/' => array(
                'POST' => 'addCompany',         //添加公司数据
            ),
            '/leocompanybk/:cid/' => array(
                'PUT' => 'updateCompany',
                'DELETE' => 'deleteCompany',
            )
        );
    }

    public function addCompanyAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $name = HttpUtilEx::getParam('name', '');
        $website = HttpUtilEx::getParam('website', '');
        $phone = HttpUtilEx::getParam('phone', '');
        $address = HttpUtilEx::getParam('address', '');
        $company = array(
            'name' => $name,
            'website' => $website,
            'phone' => $phone,
            'address' => $address,
        );
        $res = LeoCompanyNamespace::addRecord($company);
        if ($res) {
            $this->output(200, $res, '操作成功');
        } else {
            $this->output(500, $res, '保存数据失败');
        }
    }

    public function updateCompanyAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $updateData = array();
        $cid = HttpUtilEx::getParam('cid', '');
        if (empty($cid)) {
            $this->output(400, '', 'id 参数不能为空');
        }
        $info = LeoCompanyNamespace::getBatchInfo([$cid]);
        if (empty($info[$cid])) {
            $this->output(404, '', '未找到对应公司');
        }
        $cmpInfo = $info[$cid];
        $name = HttpUtilEx::getParam('name', -1);
        if ($name != -1 && $name != $cmpInfo['name']) {
            $updateData['name'] = $name;
        }
        $website = HttpUtilEx::getParam('website', -1);
        if ($website != -1 && $website != $cmpInfo['website']) {
            $updateData['website'] = $website;
        }
        $phone = HttpUtilEx::getParam('phone', -1);
        if ($phone != -1 && $phone != $cmpInfo['website']) {
            $updateData['phone'] = $phone;
        }
        $address = HttpUtilEx::getParam('address', -1);
        if ($address != -1 && $address != $cmpInfo['address']) {
            $updateData['address'] = $address;
        }
        $res = LeoCompanyNamespace::updateInfo($cid, $updateData);
        if ($res) {
            $this->output(200, [], '操作成功');
        } else {
            $this->output(500, [], '更新失败');
        }
    }

    //删除一条记录, 实际是把状态置为-1
    public function deleteCompanyAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $cid = HttpUtilEx::getParam('cid', '');
        if (empty($cid)) {
            $this->output(400, '', 'id 参数不能为空');
        }
        $info = LeoCompanyNamespace::getBatchInfo([$cid]);
        if (empty($info[$cid])) {
            $this->output(404, '', '未找到对应公司');
        }
        $update = array('status' => -1, 'update_t' => time());
        $res = LeoCompanyNamespace::updateInfo($cid, $update);
        if ($res) {
            $this->output(200, [], '操作成功');
        } else {
            $this->output(500, [], '删除失败');
        }
    }
}
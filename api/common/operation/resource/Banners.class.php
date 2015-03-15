<?php

/**
 * 运营banner
 * @author lirui1 <lirui1@ganji.com>
 * @version 2015/01/20
 * @copyright ganji.com
 */
require_once CLIENT_API . '/common/operation/model/BannersModel.class.php';

class Banners extends ResourceBase {

    public function setUriMatchConfig() {
        return array(
            '/banners/' => array(
                'GET' => 'banner', //上传图片
            ),
        );
    }

    public function setParamConfig() {
        return array(
            'banner' => array(
                'installId' => 'int',
                'customerId' => 'int',
                'versionId' => 'string',
                'clientAgent' => 'string',
                'agency' => 'string',
                'city_id' => 'int',
                'page_type' => 'int',
                'category_id' => 'int',
                'majorCategory_id' => 'int',
            ),
        );
    }

    public function bannerAction() {
        if ($this->_param['city_id'] <= 0 || $this->_param['page_type'] <= 0) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_PARAM, '', 'city_id或page_type参数错误');
            ResourceBase::display($res);
            return;
        }
        $bannersModel = new BannersModel($this->_param);
        $banners = $bannersModel->getBannerList();
        $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS, '', '', self::formatBannerList($banners));
        ResourceBase::display($res);
    }

    /**
     * 格式化banner列表
     * @param type $banners
     * @return array
     */
    private static function formatBannerList($banners) {
        $result = array();
        $tmp = array();
        if (!is_array($banners) || empty($banners)) {
            return $result;
        }
        foreach ($banners as $banner) {
            foreach ($banner as $column => $value) {
                switch ($column) {
                    case 'id':
                    case 'banner_type':
                        $tmp[$column] = $value;
                        break;
                    case 'base':
                        $tmp = array_merge($tmp, $value);
                        break;
                    default:
                        $tmp['detail'][$column] = $value;
                        break;
                }
            }
            $result[] = $tmp;
        }
        return $result;
    }

}

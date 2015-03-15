<?php
/**
 * 获取地理信息接口
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2015/01/05
 * @copyright ganji.com
 */

require_once CODE_BASE2 . '/app/geo/GeoNamespace.class.php';

class Geo extends ResourceBase {

    public function setUriMatchConfig() {
        return array(
            '/geo/province/' => array(
                'GET' => 'getAllProvince',       //获取所有省份
            ),
            '/geo/province/:province_id/cities/' => array(   //获取某一省份下所有城市
                'GET' => 'getCityListByProvince',
            ),
            '/geo/city/' => array(                      //获取所有城市列表
                'GET' => 'getAllCityList',
            ),
            '/geo/city/:city_id/district/' => array(
                'GET' => 'getAllDistrictAndStreet',     //获取城市下面区域和街道
            ),
        );
    }
    public function setParamConfig() {
        return array(
            'getAllProvince' => array(
                'd_version' => 'string',
            ),
            'getCityListByProvince' => array(
                'province_id' => 'int',
                'd_version' => 'string',
            ),
            'getAllCityList' => array(
                'type' => 'classify',           //按中文首字母排序
                'd_version' => 'string',
            ),
            'getAllDistrictAndStreet' => array(
                'city_id' => 'int',
                'd_version' => 'string',
            ),
        );
    }
    public function checkParam($paramKey) {
        switch ($paramKey) {
            case 'province_id':
                if ($this->_param[$paramKey] <= 0) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_URI_NOT_FOUND, '', 'province_id error.');
                }
                break;

            case 'city_id':
                if ($this->_param[$paramKey] <= 0) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_URI_NOT_FOUND, '', 'city_id error');
                }
                break;
        }
        return true;
    }

    public static $GEO_VERSION = array(
        'province' => '20140105144612',
        'city' => '20150116184512',
    );

    /**
     * 获取所有省份城市信息
     * GET /api/common/default/geo/province/?d_version=2015020112121
     */
    public function getAllProvinceAction() {
        if (!empty($this->_param['d_version']) && $this->_param['d_version'] == self::$GEO_VERSION['province']) {
            //不需要更新直接返回204
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_SUCCESS, BaseStatusCode::HTTP_304, '');
            ResourceBase::display($res);return;
        }

        $returnList = array();
        $provinceList = GeoNamespace::getAllProvince();
        foreach ($provinceList as $provinceItem) {
            if ($provinceItem['id'] == 32) {    //省份为其他，暂时忽略
                continue;
            }
            // $provinceInfo = GeoNamespace::getProvinceById($provinceItem['id']);
            // var_dump($provinceItem, $provinceInfo);exit;
            $tempProvince = array(
                'id' => $provinceItem['id'],
                'name' => $provinceItem['name'],
            );
            $returnList['list'][] = $tempProvince;
        }
        $returnList['d_version'] = self::$GEO_VERSION['province'];
        $res = ResourceBase::formatRes(BaseStatusCode::ERR_SUCCESS, '', '', $returnList);
        ResourceBase::display($res);return;
    }
    //格式化城市数据信息
    private function _formatCityInfo($cityInfo) {
        return array(
            'city_id' => $cityInfo['id'],
            'city_code' => $cityInfo['city_code'],
            'name' => $cityInfo['name'],
            'pinyin' => $cityInfo['pinyin'],
            'parent_id' => $cityInfo['parent_id'],
        );
    }

    /**
     * GET /api/common/default/province/1/cities/?d_version=20150116184512
     */
    public function getCityListByProvinceAction() {
        if ($this->_param['d_version'] == self::$GEO_VERSION['city']) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS, CommonErrCode::HTTP_304);
            ResourceBase::display($res);return;
        }
        $returnRes = array();
        //处理省份下面城市
        $cities = GeoNamespace::getChildByProvinceId($this->_param['province_id']);
        foreach ($cities as $cityItem) {
            $returnRes['list'][] = $this->_formatCityInfo($cityItem);
        }
        $returnRes['d_version'] = self::$GEO_VERSION['city'];
        $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS, '', '', $returnRes);
        ResourceBase::display($res);return;
    }

    /**
     * GET /api/common/default/geo/city/?type=classify&d_version=20150116184512
     */
    public function getAllCityListAction() {
        if ($this->_param['d_version'] == self::$GEO_VERSION['city']) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS, CommonErrCode::HTTP_304);
            ResourceBase::display($res);return;
        }
        $allCity = GeoNamespace::getAllCity();
        $returnRes = array();
        if ($this->_param['type'] == 'classify') {  //特定格式化
            $returnRes = $this->_formatClassifyRes($allCity);
        } else {
            foreach ($allCity as $cityItem) {
                $returnRes['list'][] = $this->_formatCityInfo($cityItem);
            }
        }
        $returnRes['d_version'] = self::$GEO_VERSION['city'];
        $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS, '', '', $returnRes);
        ResourceBase::display($res);return;
    }
    private function _formatClassifyRes($allCity) {
        //热门城市配置
        $hotCity = array(
            //北京，上海，广州，深圳，武汉，成都，重庆，常州，天津，南京，大连
            12,    13,  16,  17,   194, 45,  15,  69,  14,   65,  56
        );
        $returnRes = array();
        require_once GANJI_V5 . '/cron/hanzitopinyin/ChineseStringToPinYin.class.php';
        $chineseStringToPinYin = new ChineseStringToPinYin();
        $hotCityArr = array();  //临时保存热门城市，确定顺序时使用
        foreach ($allCity as $cityItem) {
            $chineseStringToPinYin->setChineseString($cityItem['name']);
            $fl = strtoupper($chineseStringToPinYin->getFirstChar());
            $cityInfo = $this->_formatCityInfo($cityItem);
            $returnRes['list'][$fl][$cityItem['pinyin']] = $cityInfo;
            if (in_array($cityItem['id'], $hotCity)) {
                $hotCityArr[$cityItem['id']] = $cityInfo;
            }
        }
        //根据全拼排序
        foreach ($returnRes['list'] as $fl => $item) {
            ksort($returnRes['list'][$fl]);
            $returnRes['list'][$fl] = array_values($returnRes['list'][$fl]);
        }
        //指定顺序处理热门城市
        foreach ($hotCity as $cityId) {
            $returnRes['hot'][] = $hotCityArr[$cityId];
        }
        ksort($returnRes['list']);
        //把hot 排入到list 中
        $returnRes['list'] = array_merge(array('hot' => $returnRes['hot']), $returnRes['list']);
        return $returnRes;
    }

    /**
     * 获取城市下面区域街道
     * GET /api/common/default/geo/city/12/district/?d_version=1.0.1384225778
     */
    public function getAllDistrictAndStreetAction() {
        require_once CLIENT_ROOT . '/api/common/default/model/MobConfigCityModel.class.php';
        $cityVersion = MobConfigCityModel::getCityVersionConfig($this->_param['city_id']);
        if (empty($cityVersion)) {
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_URI_NOT_FOUND, '', 'city_id not found cityVersion.');
            ResourceBase::display($res);return;
        }
        if (!empty($this->_param['d_version']) && $cityVersion[0]['version'] == $this->_param['d_version']) {
            //不需要更新直接返回204
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_SUCCESS, BaseStatusCode::HTTP_304, '');
            ResourceBase::display($res);return;
        }
        //获取区域列表
        $districtList = GeoNamespace::getChildByCityId($this->_param['city_id']);
        if (empty($districtList)) {
            $res = ResourceBase::formatRes(BaseStatusCode::ERR_URI_NOT_FOUND, '', 'city_id not found district.');
            ResourceBase::display($res);return;
        }
        $returnList = array();
        $tempDistrict = array();
        foreach ($districtList as $districtItem) {
            $tempDistrict = array(
                'id' => $districtItem['id'],
                'name' => $districtItem['name'],
            );
            //处理街道
            $streetList = GeoNamespace::getChildByDistrictId($districtItem['id']);
            if (!empty($streetList)) {
                foreach ($streetList as $streetItem) {
                    $tempDistrict['streets'][] = $this->_formatStreetInfo($streetItem);
                }
            }
            $returnList['list'][] = $tempDistrict;
        }
        $returnList['d_version'] = $cityVersion[0]['version'];
        $res = ResourceBase::formatRes(BaseStatusCode::ERR_SUCCESS, '', '', $returnList);
        ResourceBase::display($res);return;
    }
    //格式化街道信息返回
    private function _formatStreetInfo($streetInfo) {
        return array(
            'id' => $streetInfo['id'],
            'name' => $streetInfo['name'],
        );
    }
}

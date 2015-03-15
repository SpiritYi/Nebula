<?php

/**
 * 图片上传接口
 * @author lirui1 <lirui1@ganji.com>
 * @version 2015/01/15
 * @copyright ganji.com
 */
require_once CLIENT_API . '/common/default/model/FilesModel.class.php';

class Files extends ResourceBase {

    public function setUriMatchConfig() {
        return array(
            '/files/' => array(
                'POST' => 'upload', //上传文件
            ),
        );
    }

    public function setParamConfig() {
        return array(
            'upload' => array(
                'count' => 'int',
                'category_id' => 'int',
                'watermark' => 'int',
                'img_width' => 'int',
                'img_height' => 'int',
                'nowatermark' => 'int',
            ),
        );
    }

    public function uploadParam() {
        $args = $this->_param;
        $result = array(
            'desc' => '手机客户端用户图片',
            'width' => $args['img_width'] > 0 ? ($args['img_width'] <= 2000 ? $args['img_width'] : 2000) : 770,
            'height' => $args['img_height'] > 0 ? ($args['height'] <= 2000 ? $args['img_height'] : 2000) : 470,
            'category_id' => $args['category_id'],
            'nowatermark' => $args['nowatermark'] <= 0 ? 1 : $args['nowatermark'],
        );
        return $result;
    }

    public function uploadAction() {
        if (empty($_FILES['file'])) {
            $res = self::formatRes(CommonErrCode::ERR_PARAM, '', '文件资源参数为空');
            self::display($res);
            return;
        }
        $mUploadImage = new UploadModel();
        //参数处理
        $params = $this->uploadParam();
        if (is_array($_FILES['file']['name'])) {//兼容客户端以上传多张的数据结构，上传一张图片
            $rs = $mUploadImage->multiUpload($_FILES['file'], $params);
            foreach ($rs as $key => $itemUrl) {
                $rs[$key] = array(
                    'url' => $itemUrl,
                );
            }
        } else {
            $rs = $mUploadImage->upload($_FILES['file'], $params);
            if ($rs == false) {
                $res = format:: codeFormat(bodyErrDef::ERROR_SYSTEM, '上传失败');
                $this->display($res);
                exit;
            }
        }
        $res = format:: codeFormat(bodyErrDef::ERROR_SUCCESS, '', array('data' => $rs));
        $this->display($res);
    }

    public function amrAction() {
        
    }

}

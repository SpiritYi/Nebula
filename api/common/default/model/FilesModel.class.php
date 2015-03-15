<?PHP

/**
 * @author lirui1 2015-01-15
 * 
 */
include_once CODE_BASE2 . '/util/http/Curl.class.php';
require_once CODE_BASE2 . '/app/category/CategoryNamespace.class.php';
require_once CLIENT_APP_DATASHARE . '/config/apiConf.class.php';

class UploadModel {

    /**
     * 上传文件种类和类型
     * @var type 
     */
    private static $fileUploadTypes = array(
        'image' => array("jpeg", "gif", "png"),
        'audio' => array("amr"),
    );
    private static $maxSize = 5242880; //上传最大大小
    private static $upload_path = '/ganji/upload_tmp/'; //上传文件目录

    /**
     * 上传文件
     * @param type $fileInfo
     * @param type $fileArgs
     * @return boolean
     */

    public static function upload($fileInfo, $fileArgs = array()) {
        $dstFile = self::$upload_path . time() . '_' . $fileInfo['name'];
        //check upload
        if ($fileInfo['error'] != 0 || !is_uploaded_file($fileInfo['tmp_name'])) {
            return false;
        }
        $fileType = self::pictype($fileInfo["tmp_name"]);
        $fileUploadDir = '';
        foreach (self::$fileUploadTypes as $listUploadType => $fileUploadType) {
            if (in_array($fileType, $fileUploadType)) {  //文件类型判断
                $fileUploadDir = $listUploadType;
                break;
            }
        }
        if (intval($fileInfo['size']) >= self::$maxSize) {// 文件大小判断
            return false;
        }
        if (!move_uploaded_file($fileInfo['tmp_name'], $dstFile)) {
            return false;
        }
        $fileArgs = self::uploadArgsFormat($fileUploadDir, $fileArgs);
        $uploadResult = self::uploadFile($dstFile, $fileArgs, $fileUploadDir);
        return self::uploadResultFormat($fileUploadDir, $uploadResult, $fileArgs);
    }

    /**
     * curl上传
     * @param type $filename
     * @param type $args
     * @param type $fileType
     * @return type
     */
    private static function uploadFile($filename, $args, $fileType = 'image') {
        $curl = new Curl();
        $postData = self::buildParam($filename, $args, $fileType);
        $json = $curl->post(GANJI_IMAGE_SERVER, $postData, true);
        $imgData = json_decode($json);
        //图片上传成功，删除本地图片
        if ($imgData && $imgData->error == 0) {
            @unlink($filename);
        }
        return $imgData;
    }

    /**
     * 上传文件参数
     * @param type $filename
     * @param type $args
     * @param type $fileType
     * @return string
     */
    private static function buildParam($filename, $args, $fileType = 'image') {
        switch ($fileType) {
            case 'image':
                //类目
                if ($args['category_id'] > 0) {
                    $category = CategoryNamespace::getCategoryById($args['category_id']);
                    $args['uploadDir'] = $category['source_name'];
                } else {
                    $args['uploadDir'] = 'ganji';
                }
                //无水印
                if ($args['nowatermark'] == 2) {
                    $args['uploadDir'] = 'nomask';
                }
                $file = '@' . $filename;
                //php 5.5兼容
                if (class_exists('\CURLFile')) {
                    $file = curl_file_create($filename);
                }
                $postData = array(
                    'file' => $file,
                    'uploadDir' => $args['uploadDir'],
                    'maxNum' => 1,
                    'maxSize' => 1024 * 1024 * 5,
                    'type' => 'jpg,jpeg,png',
                    'typeDescription' => $args['desc'],
                    'resizeImage' => 'true',
                    'resizeWidth' => $args['width'],
                    'resizeHeight' => $args['height'],
                    'resizeCutEnable' => 'false',
                    'createMiddle' => 'false',
                    'createThumb' => 'false',
                    'debug' => 'false',
                );
                break;
            case 'audio':
                $postData = array(
                    'file' => $file,
                    'uploadDir' => 'audio',
                    'maxNum' => 1,
                    'maxSize' => 1024 * 1024 * 5,
                    'type' => 'amr',
                    'typeDescription' => $args['desc'],
                );
                break;
        }
        return $postData;
    }

    /**
     * 图片上传参数格式化，
     * @param type $fileUploadDir 上传目录
     * @param string $fileArgs    参数
     */
    private static function uploadArgsFormat($fileUploadDir, $fileArgs) {
        switch ($fileUploadDir) {
            case 'audio':
                $fileArgs['desc'] = '手机客户端音频文件';
                break;
        }
        return $fileArgs;
    }

    /**
     * 返回数据格式化
     * @param type $fileUploadDir
     * @param type $uploadResult
     */
    private static function uploadResultFormat($fileUploadDir, $uploadResult, $fileArgs) {
        $result = '';
        if ($uploadResult->error != 0) {
            Logger::logError(sprintf('upload fail:args[%s] result:[%s],', $fileArgs, $uploadResult), 'File.Upload');
            return false;
        }
        switch ($fileUploadDir) {
            case 'audio':
                $result = MobConfig::GANJI_IMAGE_DOMAIN . $uploadResult->info[0]->url;
                break;
            case 'image':
                $result = self::formatImageUrl($uploadResult->info[0]->url, $fileArgs['width'], $fileArgs['height']);
                break;
        }
        return $result;
    }

    /**
     * 多文件上传
     * @param type $fileInfo
     * @param type $fileArgs
     * @return type
     */
    public static function multiUpload($fileInfos, $fileArgs = array()) {
        $count = count($fileInfos['name']);
        $rs = array();
        $fileinfoKeys = array_keys($fileInfos);
        for ($i = 0; $i < $count; $i++) {
            foreach ($fileinfoKeys as $fileinfoKey) {
                $fileInfo[$fileinfoKey] = $fileInfos[$fileinfoKey][$i];
            }
            $rs[$i] = self::upload($fileInfo, $fileArgs);
        }
        return $rs;
    }

    private static function pictype($file) {
        /* $png_header = "/x89/x50/x4e/x47/x0d/x0a/x1a/x0a";
          $jpg_header = "/xff/xd8"; */
        $header = file_get_contents($file, 0, NULL, 0, 5);
        //echo bin2hex($header);
        if ($header { 0 } . $header { 1 } == "\x89\x50") {
            return 'png';
        } else if ($header { 0 } . $header { 1 } == "\xff\xd8") {
            return 'jpeg';
        } else if ($header { 0 } . $header { 1 } . $header { 2 } == "\x47\x49\x46") {
            return 'gif';
        } else if ($header == '#!AMR') {//amr音频格式
            return 'amr';
        }
        return 'unknow';
    }

    private static function formatImageUrl($imageUrl, $width, $height) {
        $imageParam = explode('.', $imageUrl);
        $imageParam2 = explode('_', $imageParam[0]);
        //如果不是正确的url ，返回false
        if (empty($imageParam2[0]) || empty($imageParam[1])) {
            return false;
        }
        return MobConfig::GANJI_IMAGE_DOMAIN . $imageParam2[0] . '_' . $width . '-' . $height . '_7-5.' . $imageParam[1];
    }

}

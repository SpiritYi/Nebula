<?php
/**
 * 显示的页面基类
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/07
 */

//初始化模板文件夹路劲
PageBase::init();

class PageBase {

    //模板所在文件夹：WWWROOT/Page/Template
    private static $_TEMPLATE_DIR_PATH;

    public static function init() {
        self::$_TEMPLATE_DIR_PATH = WEBSITE . '/template';
    }

    /**
     * 加载模板文件
     * @param $templateFile string 文件路径，相对文件夹Page/App/Template
     */
    public function render($templateFile = '', $relateFolder = '') {
        header('Content-Type:text/html; charset=UTF-8');
        $pageHTML = $this->fetch($templateFile, $relateFolder);
        echo $pageHTML;
    }

    public function fetch($templateFile, $relateFolder = '') {
        if (empty($templateFile)) {
            trigger_error("未只指定页面模板", E_USER_ERROR);
        }

        ob_start();
        //手动指定模板相对文件夹
        if (!empty($relateFolder)) {
            self::$_TEMPLATE_DIR_PATH = $relateFolder;
        }
        require_once self::$_TEMPLATE_DIR_PATH . '/' . $templateFile;
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }

    //页面访问权限控制
    const VERIFY_USER_KEY = 'verify_user';
    public function accessVerify() {
        require_once CODE_BASE . '/util/http/CookieUtil.class.php';

        // CookieUtil::write(self::VERIFY_USER_KEY, 'admin.test_1428422400', 30 * 24 * 3600);
        // var_dump('here');exit;

        $allow = array(
            'admin.test',
        );
        $cookieUserStr = CookieUtil::read(self::VERIFY_USER_KEY);
        preg_match('/^(.*)_([\d]{8,})$/', $cookieUserStr, $dataArr);     //cookie 数据本身加到期时间戳，防止抓取伪造, 格式为admintest_1428422400
        if (empty($dataArr) || $dataArr[2] < time() || !in_array($dataArr[1], $allow)) {
            header('location:/verify');     //跳转到认证页面
        }
    }

    /**
     * <head> 标签中条目输出，如果是文件自动加载，字符串直接输出
     */
    public function headExport($content) {
        preg_match('/^.*\.([\w]{1,10})$/', $content, $match);
        if (empty($match)) {
            echo $content; return;
        }
        $fileUrl = DomainConfig::STA_DOMAIN . $content;
        switch (strtolower($match[1])) {
            case 'css':
                echo '<link type="text/css" rel="stylesheet" href="' . $fileUrl . '" >'; return;
                break;

            case 'js':
                echo '<script type="text/javascript" src="'. $fileUrl . '"></script>'; return;
                break;
        }
    }

    /**
     * 获取静态引入文件
     * @param $type string enum[CSS, JS]
     * @param $fileName string 相对StaticFile 文件夹下相应分类文件夹的相对路径，例：相对SstaticFile/CSS 路径
     * @return string
     */
    public function staticFileLink($type, $fileName) {
        $fileUrl = DomainConfig::STA_DOMAIN . $fileName;
        switch ($type) {
            case 'CSS':
                $linkString = '<link type="text/css" rel="stylesheet" href="' . $fileUrl . '" >';
                return $linkString;
                break;

            case 'JS':
                $linkString = '<script type="text/javascript" src="'. $fileUrl . '"></script>';
                return $linkString;

            default:
                # code...
                break;
        }
    }
}

<?php
/**
 * 显示的页面基类
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/07
 */

//初始化模板文件夹路径
PageBase::init();

class PageBase {

    //模板所在文件夹：WWWROOT/Page/Template
    private static $_TEMPLATE_DIR_PATH;

    public static function init($tempDir = '') {
        self::$_TEMPLATE_DIR_PATH = empty($tempDir) ? WEBSITE . '/template' : $tempDir;
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

    public static function templateFileExists($templateFile) {
        return file_exists(self::$_TEMPLATE_DIR_PATH . '/' . $templateFile);
    }

    //页面访问权限控制
    public function accessVerify() {
        require_once CODE_BASE . '/util/http/CookieUtil.class.php';
        require_once CODE_BASE . '/app/user/UserNamespace.class.php';
        $cookieUserStr = CookieUtil::read(UserNamespace::USER_VERIFY_COOKIE_KEY);
        return UserNamespace::getCookieUser($cookieUserStr);
    }

    /**
     * <head> 标签中条目输出，如果是文件自动加载，字符串直接输出
     */
    public function staExport($content) {
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
}

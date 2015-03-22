<?php
/**
 * 公司说明，关于我们页
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/08
 * @copyright nebula.com
 */

require_once WEBSITE . '/app/Master.class.php';

require_once WEBSITE . '/model/ArticleModel.class.php';

class AboutPage extends Master {
    public function loadHead() {
        $this->staExport('<title>关于公司</title>');
    }

    public function action() {
        $this->render('company/about.php');
    }

    //加载一篇文章列表
    public function showArticle($templateFile) {
        $noticeInfo = $this->searchArticle($templateFile);
        if (empty($noticeInfo))
            return '';
        $html = "
            <div class=\"col-lg-4\">
                <h3>{$noticeInfo[0]['title']}</h3>
                <p class=\"note\">" . date('Y/m/d', $noticeInfo[0]['p_time']) . "</p>
                <p>{$noticeInfo[0]['brief']}</p>
                <p><a class=\"btn btn-default\" href='/article/article?id={$noticeInfo[0]['id']}' role=\"button\">查看详情 &raquo;</a></p>
            </div>";
        return $html;
    }

    //根据模板文件名找数据文章记录
    public function searchArticle($tempFile) {
        $handle = BaseMainModel::getDbHandle();
        $sqlString = "SELECT id, title, brief, template, p_time FROM " . ArticleModel::getTable() . " WHERE template LIKE '%{$tempFile}'";
        $result = DBMysqlNamespace::query($handle, $sqlString);
        return  $result;
    }
}

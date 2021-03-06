<?php
/**
 * 静态文章落地页
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/22
 * @copyright nebula.com
 */

require_once WEBSITE . '/app/Master.class.php';
require_once CODE_BASE . '/model/company/ArticleModel.class.php';

class ArticlePage extends Master {
    public function loadHead() {
        $this->staExport('<title>星云财富基金</title>');
    }

    public function action() {
        $id = HttpUtil::getParam('id');
        $this->articleInfo = ArticleModel::getArticleInfo($id);
        if (empty($this->articleInfo) || !PageBase::templateFileExists($this->articleInfo[0]['template'])) {
            $this->render('/error/content_error_404.php');
            return;
        }
        $this->render($this->articleInfo[0]['template']);

        //输出模板文件路径
        $tempalteHtml = '<!-- ' . $this->articleInfo[0]['template'] . ' -->';
        $this->staExport($tempalteHtml);
    }
}

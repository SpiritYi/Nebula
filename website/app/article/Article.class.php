<?php
/**
 * 静态文章落地页
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/22
 * @copyright nebula.com
 */

require_once WEBSITE . '/app/Master.class.php';
require_once WEBSITE . '/model/ArticleModel.class.php';

class ArticlePage extends Master {
    public function loadHead() {

    }

    public function action() {
        $id = HttpUtil::getParam('id');
        $this->articleInfo = ArticleModel::getArticleInfo($id);
        if (empty($this->articleInfo) || !PageBase::templateFileExists($this->articleInfo[0]['template'])) {
            $this->render('/error/content_error_404.php');
            return;
        }
        $this->render($this->articleInfo[0]['template']);
    }
}

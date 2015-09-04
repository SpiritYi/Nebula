<?php
/**
 * Article 后台操作接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/03
 * @copyright nebula-fund.com
 */

class ArticleBKRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/articlebk/' => array(
                'POST' => 'publishArticle',     //发布文章
            ),
        );
    }

    public function publishArticleAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }

        $type = HttpUtil::getParam('type');
        $time = strtotime(HttpUtil::getParam('time'));
        $title = HttpUtil::getParam('title');
        $content = HttpUtil::getParam('content');
        $template = HttpUtil::getParam('template');

        require_once CODE_BASE . '/model/company/ArticleModel.class.php';
        if (!array_key_exists($type, ArticleModel::$TYPE_NAME) || $time <= 0 ||
                empty($title) || empty($content)) {
            $this->output(400, '', '数据填写不正确');
        }
        //校验文章页面文件是否存在
        require_once CODE_BASE . '/app/page/PageBase.class.php';
        if (!PageBase::templateFileExists($template)) {
            $this->output(400, '', '页面文件路径错误');
        }

        $data = array(
            'type' => $type,
            'title' => $title,
            'brief' => $content,
            'template' => $template,
            'p_time' => $time,
            'status' => 1,
        );
        require_once BACKEND . '/web/model/company/ArticleBKModel.class.php';
        $res = ArticleBKModel::addArticle($data);
        if ($res) {
            $this->output(200, '', '发布成功');
        } else {
            $this->output(500, '', '服务器错误，保存失败');
        }
    }
}

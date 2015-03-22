<?php
/**
 * 公共系统
 * @auhtor chenyihong <jinglingyueyue@gmial.com>
 * @version 2015/03/21
 * @copyright nebula.com
 */

require_once WEBSITE . '/app/Master.class.php';
require_once WEBSITE . '/model/ArticleModel.class.php';

class NoticeListPage extends Master {
    public function loadHead() {

    }

    public function action() {
        $this->noticeList = ArticleModel::getNoticeList();
        if (empty($this->noticeList)) {
            $this->render('/error/content_error_500.php');
            return;
        }
        $this->render('notice/notice_list.php');
    }
}

<?php
/**
 * 公共系统
 * @auhtor chenyihong <jinglingyueyue@gmial.com>
 * @version 2015/03/21
 * @copyright nebula.com
 */

require_once WEBSITE . '/app/Master.class.php';
require_once CODE_BASE . '/model/company/ArticleModel.class.php';

class NoticeListPage extends Master {
    public function loadHead() {
        $this->staExport('<title>公告板</title>');
    }

    public function action() {
        $status = HttpUtil::getParam('status', 0);
        $this->noticeList = ArticleModel::getNoticeList($status);
        if (empty($this->noticeList)) {
            $this->render('/error/content_error_500.php');
            return;
        }
        $this->render('notice/notice_list.php');
    }
}

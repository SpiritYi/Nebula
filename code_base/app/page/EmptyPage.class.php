<?php
/**
 * 空白页基类
 * @auhtor chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/08
 * @copyright nebula.com
 */
require_once CODE_BASE . '/app/page/PageBase.class.php';
// require_once CODE_BASE . '/App/UserCenterNamespace.class.php';

abstract class EmptyPage extends PageBase{

    public function __construct() {
        PageBase::render('empty.php');
    }

    /**
     * 输出内容到模板页的<head></head> 中
     * 函数内部直接采取echo 形式
     */
    abstract public function loadHead();

    /**
     * 加载主体内容到模板页中间
     * 加载代码例：$this->render('template.php');
     */
    // abstract public function loadContent();

    abstract public function action();

}

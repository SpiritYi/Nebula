<?php
/**
 * Stock 空页面基类
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/22
 * @copyright nebula-fund.com
 */

abstract class StockEmpty extends PageBase {
    public function __construct() {
        PageBase::render('/stock_empty.php');
    }

    abstract function loadHead();

    abstract function action();
}

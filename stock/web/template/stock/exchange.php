<style type="text/css">
    #record_list thead {
        color: #D0D0D0;
    }
    #record_list td {
        padding: 12px 8px;
    }
    .pagination span {
        padding: 2px 4px;
    }
</style>

<div class="container">
    <div>
        <table class="table table-bordered" id="record_list">
            <thead>
                <tr>
                    <th>成交时间</th>
                    <th>股票代码</th>
                    <th>股票名称</th>
                    <th>类型</th>
                    <th class="text-right">委托价</th>
                    <th class="text-right">成交价</th>
                    <th class="text-right">数量</th>
                    <th class="text-right">印花税</th>
                    <th class="text-right">佣金</th>
                    <th class="text-right">实现盈亏</th>
                    <th>备注</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->exchangeList as $rcdItem) { 
                    $earnTypeClass = '';
                    if ($rcdItem['earn'] > 0) {
                        $earnTypeClass = 'red';
                    } else if ($rcdItem['earn'] < 0) {
                        $earnTypeClass = 'green';
                    } ?>
                    <tr>
                        <td><?php echo date('Y/m/d H:i:s', $rcdItem['time']); ?></td>
                        <td><?php echo $rcdItem['sid']; ?></td>
                        <td><?php echo $rcdItem['sname']; ?></td>
                        <td><?php echo $rcdItem['direction'] == 1 ? '买入' : '卖出'; ?></td>
                        <td class="text-right"><?php echo $rcdItem['delegate_price'] == -1 ? '现价' : StockCompanyNamespace::showPrice($rcdItem['delegate_price']); ?></td>
                        <td class="text-right"><?php echo StockCompanyNamespace::showPrice($rcdItem['strike_price']); ?></td>
                        <td class="text-right"><?php echo $rcdItem['count']; ?></td>
                        <td class="text-right"><?php echo StockCompanyNamespace::showPrice($rcdItem['tax']); ?></td>
                        <td class="text-right"><?php echo StockCompanyNamespace::showPrice($rcdItem['commission']); ?></td>
                        <td class="text-right <?php echo $earnTypeClass; ?>"><?php echo StockCompanyNamespace::showPrice($rcdItem['earn']); ?></td>
                        <td><?php echo $rcdItem['desc']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="text-center">
        <ul class="pagination">
            <?php if ($this->page == 1) { ?>
                <li class="disabled"><a><span>上一页</span></a></li>
            <?php } else { ?>
                <li><a href="<?php echo $this->pageUrlTmp . ($this->page -1); ?>"><span>上一页</span></a></li>
            <?php } ?>
            <?php if ($this->indexStart > 1) { ?>
                <li><a href="<?php echo $this->pageUrlTmp . '1'; ?>"><span>1</span></a></li>
            <?php } ?>
            <?php if ($this->indexStart > 2) { ?>
                <li class="disabled"><a><span>...</span></a></li>
            <?php } ?>
            <?php for ($i = $this->indexStart; $i <= $this->indexEnd; $i ++) {
                if ($this->page == $i) { ?>
                    <li class="active"><a><span><?php echo $i; ?></span></a></li>
                <?php } else { ?>
                    <li><a href="<?php echo $this->pageUrlTmp . $i; ?>"><span><?php echo $i; ?></span></a></li>
                <?php } ?>
            <?php } ?>
            <?php if ($this->indexEnd < $this->pageTotal - 1) { ?>
                <li class="disabled"><a><span>...</span></a></li>
            <?php } ?>
            <?php if ($this->indexEnd < $this->pageTotal) { ?>
                <li><a href="<?php echo $this->pageUrlTmp . $this->pageTotal; ?>"><span><?php echo $this->pageTotal; ?></span></a></li>
            <?php } ?>
            <?php if ($this->page == $this->pageTotal) { ?>
                <li class="disabled"><a><span>下一页</span></a></li>
            <?php } else { ?>
                <li><a href="<?php echo $this->pageUrlTmp . ($this->page + 1); ?>"><span>下一页</span></a></li>
            <?php } ?>
        </ul>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#navbar_record'));
    });
</script>
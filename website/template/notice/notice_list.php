<div class="container">
    <?php foreach ($this->noticeList as $notice) { ?>
        <div class="col-lg-4">
            <h3><?php echo $notice['title']; ?></h3>
            <p class="note"><?php echo date('Y/m/d', $notice['p_time']); ?></p>
            <p><?php echo $notice['brief']; ?></p>
            <p><a class="btn btn-default" href="/article/article?id=<?php echo $notice['id']; ?>">查看详情 &raquo;</a></p>
        </div>
    <?php } ?>
    <!--
    <div class="col-lg-4">
        <h3>2015年3月业绩公告</h3>
        <p>3月收益率12.89%，大盘涨幅6%。</p>
        <p><a class="btn">查看详情</a></p>
    </div>
    <div class="col-lg-4">
        <h3>2015年3月业绩公告</h3>
        <p>3月收益率12.89%，大盘涨幅6%。3月收益率12.89%，大盘涨幅6%， 3月收益率12.89%，大盘涨幅6%</p>
        <p><a class="btn btn-default" href='#' role="button">查看详情</a></p>
    </div>
    <div class="col-lg-4">
        <h3>2015年3月业绩公告</h3>
        <p>3月收益率12.89%，大盘涨幅6%。</p>
        <p><a class="btn">查看详情</a></p>
    </div> -->
</div>

<script type="text/javascript">
    seajs.use(['script/base/page'], function(page) {
        // $('#navbar_notice').addClass('active');
        page.initNavBar($('#navbar_notice'));
    })
</script>

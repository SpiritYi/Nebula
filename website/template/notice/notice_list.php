<style type="text/css">
    .col-lg-4 {
        margin: 0px;
    }
</style>
<div class="container">
    <?php
        $index = 0;
        $html = ['', '', ''];
        foreach ($this->noticeList as $notice) {
            $html[$index % 3] .= '
                <div class="row" style="margin-bottom: 25px">
                    <div class="col-lg-12">
                        <h3>' . $notice['title'] . '</h3>
                        <p class="note">' . date('Y/m/d', $notice['p_time']) . '</p>
                        <p>' . $notice['brief'] . '</p>
                        <p><a class="btn btn-default" href="/article/article?id=' . $notice['id'] . '">查看详情 &raquo;</a></p>
                    </div>
                </div>';
            $index ++;
        }
    ?>
    <div class="col-lg-4">
        <?php echo $html[0]; ?>
    </div>
    <div class="col-lg-4">
        <?php echo $html[1]; ?>
    </div>
    <div class="col-lg-4">
        <?php echo $html[2]; ?>
    </div>
    <!--
    <div class="col-lg-4">
        <h3>2015年3月业绩公告</h3>
        <p>3月收益率12.89%，大盘涨幅6%。</p>
        <p><a class="btn">查看详情</a></p>
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

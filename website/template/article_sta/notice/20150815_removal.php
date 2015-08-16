<div class="container">
    <div class="page-header">
        <h3>网站服务器迁移维护通知</h3>
    </div>
    <p class="note"><?php echo date('Y/m/d', $this->articleInfo[0]['p_time']); ?></p>
    <p>网站将于 <code>2015/08/16 10:00 ~ 2015/08/16 19:00</code> 进行搬迁维护</p>
    <p>维护期间，理论上所有服务均可正常使用，本次维护是不停服搬迁维护。如有异常请等待我们修复，如超过维护时间仍出现访问异常或数据错误的情况，请及时向我们反馈，
    我们将第一时间核对、修复问题</p>
    <p>维护主要内容是对网站代码、数据库做迁移。由 <b>亚马逊AWS</b> 免费的云服务转移到 <b>DigitalOcean</b> 付费的VPS 上，服务器所属数据中心由 东京 变为 新加坡 ，
    从测试结果看，访问新加坡数据中心有更少的延迟，更稳定的网络。同时从免费到付费，我们将获得更持久、稳定的服务，服务器性能也将大幅提升，可操作的自由度更是前所未有，
    在此基础上，能够满足基金推出更多服务，也能够让服务更加全面、更加细致</p>
    <p>在此感谢各用户的理解和支持</p>
    <p>-- 星云财富基金</p>
    <p><a class="btn btn-default" href="/article/noticelist">&laquo; 返回列表</a></p>
</div>

<script type="text/javascript">
    seajs.use([], function() {
        $('#navbar_notice').addClass('active');
    })
</script>

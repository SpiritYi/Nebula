<div class="container">
    <div class="row row-offcanvas row-offcanvas-right">
        <div class="col-lg-9">
            <?php $this->userAction(); ?>
        </div>
        <div class="col-lg-3 sidebar-offcanvas">
            <div class="list-group">
                <a id="nav_u_property" class="list-group-item" href="/user/property">资产首页</a>
                <a id="nav_u_contract" class="list-group-item" href="/user/contract">我的合约</a>
                <a id="nav_u_profile" class="list-group-item" href="/user/profile">修改资料</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#navbar_user'));
    });
</script>

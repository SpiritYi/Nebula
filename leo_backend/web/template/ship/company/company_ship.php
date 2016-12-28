<div>
    <div class="col-lg-2">
        <input type="button" id="earnings_rate_modal_btn" class="btn btn-default" value="盈收配置" />
        <?php $this->render('/ship/company/earnings_bk.php'); ?>
    </div>

    <div class="col-lg-2">
        <input type="button" id="article_modal_btn" class="btn btn-default" value="发布文章" />
        <?php $this->render('/ship/company/article_bk.php'); ?>
    </div>

    <div class="col-lg-2">
        <input type="button" id="property_rate_modal_btn" class="btn btn-default" value="资产比例">
        <?php $this->render('/ship/company/property_rate_bk.php'); ?>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#nav_companyship'));

        $('#earnings_rate_modal_btn').click(function() {
            $('#earnings_rate_modal').modal({backdrop: 'static'});
        });

        $('#article_modal_btn').click(function() {
            $('#article_modal').modal({backdrop: 'static'});
        });

        $('#property_rate_modal_btn').click(function() {
            $('#property_rate_modal').modal();
        });
    });
</script>

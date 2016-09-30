<div>
    <div class="col-lg-2">
        <input type="button" id="buy_stock_btn" class="btn btn-default" value="购买股票" />
        <?php $this->render('/ship/stock/buy_stock.php'); ?>
    </div>
    <div class="col-lg-2">
        <input type="button" id="forex_asset_btn" class="btn btn-default" value="外汇总值" />
        <?php $this->render('/ship/stock/forex_asset.php'); ?>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#nav_exchange'));

        $('#buy_stock_btn').click(function() {
            $('#buy_modal').modal({backdrop: 'static'});
        });

        $('#forex_asset_btn').click(function() {
            $('#asset_modal').modal({backdrop: 'static'});
        });
    });
</script>

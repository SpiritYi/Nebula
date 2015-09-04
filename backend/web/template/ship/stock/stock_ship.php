<div>
    <input type="button" id="buy_stock_btn" class="btn btn-default" value="购买股票" />
    <?php $this->render('/ship/stock/buy_stock.php'); ?>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#nav_exchange'));

        $('#buy_stock_btn').click(function() {
            $('#buy_modal').modal({backdrop: 'static'});
        });
    });
</script>

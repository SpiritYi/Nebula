<style type="text/css">
    .submit img {
        margin-left: 15px;
    }
</style>
<div class="container">
    <div class="col-lg-4">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-4 control-label">持股公司</label>
                <div class="col-sm-8">
                    <select id="stock_select" class="selectpicker" data-width="100%">
                        <?php foreach ($this->userStockList as $item) { ?>
                            <option value="<?php echo $item['sid']; ?>"><?php echo $item['sname']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">止损价格</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="price">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8 submit" >
                    <button type="button" class="btn btn-default" id="setlimit">提交</button>
                    <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB', 'Stock'], function(NB, Stock) {
        NB.navActive($('#navbar_losslimit'));

        $('#setlimit').click(function() {
            var sid = $('#stock_select').val(), price = $('#price').val();
            console.log(sid);

            NB.apiAjax({
                loading: $('.submit img'),
                type: 'PUT',
                data: {"price":price},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/user/stock/' + sid + '/losslimit/',
                success: function(data) {
                    $('#price').val('');
                    NB.alert(data.message, 'success');
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            })
        });
    })
</script>

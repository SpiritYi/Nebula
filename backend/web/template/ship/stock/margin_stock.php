<style type="text/css">
    .submit-group img {
        margin-right: 15px;
    }
</style>
<div id="margin_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>信用交易 - 卖空</h4>
            </div>
            <div class="modal-body row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="sr-only">用户</label>
                        <select id="margin_user" class="selectpicker" data-width="100%">
                            <?php foreach ($this->stockUserList as $userInfo) { ?>
                                <option value="<?php echo $userInfo['uid']; ?>"><?php echo $userInfo['nickname']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <form class="form-inline">
                    <div class="col-lg-12 row">
                        <div class="col-lg-4">
                            <p>公司</p>
                        </div>
                        <div class="col-lg-4">
                            <p>价格</p>
                        </div>
                        <div class="col-lg-4">
                            <p>数量</p>
                        </div>
                    </div>
                    <div class="col-lg-12 row">
                        <div class="col-lg-4">
                            <input type="text" class="form-control" id="margin_stockname" data-provide="typeahead" title="股票标的">
                        </div>
                        <div class="col-lg-4">
                            <input type="text" id="margin_price" class="form-control" title="购买价格" />
                        </div>
                        <div class="col-lg-4">
                            <input type="text" id="margin_count" class="form-control" title="购买数量" value="2000"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer submit-group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <button type="button" id="trade_submit" class="btn btn-primary">提 交</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB', 'Stock'], function(NB, Stock) {

        //用户搜索股票
        Stock.initStockSelect({
            selector: $('#margin_stockname'),
            updaterBack: function() {
                var sid = $('#margin_stockname').data('sid');
                NB.apiAjax({
                    type: 'GET',
                    url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/company/information/market/' + sid + '/',
                    success: function(data) {
                        $('#margin_price').val(data.data.price);
                    }
                })
            }
        });

        //提交购买
        $('#trade_submit').click(function() {
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {uid: $('#margin_user').val(), sid: $('#margin_stockname').data('sid'), price: $('#margin_price').val(), count: $('#margin_count').val()},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/stock/marginbk/short_selling/',
                success: function(data) {
                    NB.alert(data.message, 'success');
                    $('#margin_price').val('');
                    $('#margin_count').val('');
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

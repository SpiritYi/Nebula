<style type="text/css">
    .submit-group img {
        margin-right: 15px;
    }
</style>
<div id="buy_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>购买股票</h4>
            </div>
            <div class="modal-body row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="sr-only">用户</label>
                        <select id="user_select" class="selectpicker" data-width="100%">
                            <?php foreach ($this->stockUserList as $userInfo) { ?>
                                <option value="<?php echo $userInfo['uid']; ?>"><?php echo $userInfo['nickname']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <label class="input-group-addon">公司</label>
                            <input type="text" class="form-control" id="stockname" data-provide="typeahead">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">价格</span>
                            <input type="text" id="buy_price" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">股数</span>
                            <input type="text" id="buy_count" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <p class="tip">可用金额</p>
                        <p id="user_money"></p>
                    </div>
                    <div class="form-group">
                        <h3 id="tip_sname"></h3>
                        <p id="tip_price"></p>
                    </div>
                    <div class="form-group" id="count-group" style="display:none">
                        <p class="tip">可买数量</p>
                        <p id="max_count"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer submit-group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <button type="button" id="buy_submit" class="btn btn-primary">提 交</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB', 'Stock'], function(NB, Stock) {

        //显示用户资本
        $('#user_select').change(function() {
            NB.apiAjax({
                type: 'GET',
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/stock/userbk/info/' + $(this).val() + '/',
                success: function(data) {
                    $('#user_money').html(parseInt(data.data.money));
                }
            })
        });
        $('#user_select').change();

        //用户搜索股票
        Stock.initStockSelect({
            selector: $('#stockname'),
            updaterBack: function() {
                var sid = $('#stockname').data('sid');
                NB.apiAjax({
                    type: 'GET',
                    url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/company/information/market/' + sid + '/',
                    success: function(data) {
                        $('#tip_sname').html(data.data.sname);

                        var priceDom = $('#tip_price');
                        priceDom.html(data.data.price);
                        //表示涨跌颜色
                        if (data.data.price > data.data.ysd_closing_price) {
                            priceDom.addClass('stock-up');
                        } else if (data.data.price < data.data.ysd_closing_price) {
                            priceDom.addClass('stock-under');
                        }
                    }
                })
            }
        });

        //填写完价格
        $('#buy_price').blur(function() {
            var price = $(this).val();
            if (price > 0) {
                var max_count = $('#user_money').html() / price / 100;
                $('#max_count').html(parseInt(max_count) * 100);
                $('#count-group').show();
            } else {
                NB.alert('价格不正确', 'danger');
            }
        });

        //提交购买
        $('#buy_submit').click(function() {
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {uid: $('#user_select').val(), sid: $('#stockname').data('sid'), price: $('#buy_price').val(), count: $('#buy_count').val()},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/stock/exchangebk/',
                success: function(data) {
                    NB.alert(data.message, 'success');
                    $('#buy_price').val('');
                    $('#buy_count').val('');
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

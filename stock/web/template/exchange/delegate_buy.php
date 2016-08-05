<div class="container">
    <div class="col-lg-4">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-4">公司</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="sid" data-provide="typeahead">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">报价方式</label>
                <div class="col-sm-8">
                    <select>
                        <option>自定义价格</option>
                        <option>现价</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">价格</label>
                <div class="col-sm-8">
                    <input type="text" id="price" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">数量</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="count" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8 submit-group">
                    <button type="button" class="btn btn-default" id="addDelegate">提交</button>
                    <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB', 'Stock'], function(NB, Stock) {
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

        //提交委托
        $('#addDelegate').click(function() {
            var direction = $('#direction').val(), sid = $('#sid').data('sid'), price = $('#price').val(), count = $('#count').val();

            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {"direction": direction, "sid": sid, "price": price, "count": count},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/exchange/delegate/',
                success: function(data) {
                    NB.alert(data.message, 'success');
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

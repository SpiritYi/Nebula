<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>编号</th>
                <th>股票代码</th>
                <th>股票名称</th>
                <th>总数量</th>
                <th>开仓价</th>
                <th>持仓成本</th>
                
                <th>现价</th>
                <th>涨跌幅</th>
                <th>市值</th>
                
                <th>盈亏率</th>
                <th>浮动盈亏</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $index = 1;
                foreach ($this->tableList as $item) { ?>
                    <tr class="margin-record" id="msr_<?php echo $item['id']; ?>" data-sid="<?php echo $item['sid']; ?>" data-cost="<?php echo $item['cost']; ?>">
                        <td class="note"><?php echo $index; ?></td>
                        <td class="sid"><?php echo $item['sid']; ?></td>
                        <td class="sname"><?php echo $item['sname']; ?></td>
                        <td class="count"><?php echo $item['count']; ?></td>
                        <td class="strike_price"><?php echo $this->showPrice($item['strike_price'], 2); ?></td>
                        <td class="note"><?php echo $this->showPrice($item['per_cost'], 3); ?></td>
                        
                        <td class="price hg-field"></td>
                        <td class="price-diff-rate hg-field"></td>
                        <td class="market-value note"></td>
                        
                        <td class="earn-rate hg-field"></td>
                        <td class="earn hg-field"></td>
                        <td>
                            <button type="button" class="btn btn-info btn-xs close-btn" data-msrid="<?php echo $item['id']; ?>">平仓</button>
                        </td>
                    </tr>
                    <?php $index ++;
                } ?>
        </tbody>
    </table>
</div>

<!-- 平仓模态框 -->
<div id="close_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>信用交易 - 平仓</h4>
            </div>
            <div class="modal-body row">
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
                            <input type="hidden" id="close_msrid" value="" />
                            <p id="close_sname" style="margin-top: 7px;"></p>
                        </div>
                        <div class="col-lg-4">
                            <input type="text" id="close_price" class="form-control" title="平仓价格" />
                        </div>
                        <div class="col-lg-4">
                            <input type="text" id="close_count" class="form-control" title="平仓数量" value="2000"/>
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
        NB.navActive($('#navbar_margin'));

        //处理是否开市状态
        var market = {is_exchange: true};
        Stock.getMarketStatus(market);

        //刷新页面报价信息
        var stockColorClass = 'stock-up stock-under';
        /**
         * 刷新页面数据
         * @param isFirst bool  //是否首次，首次强刷
         */
        function refreshData(isFirst) {
            if (!isFirst && !market.is_exchange) {
                return;
            }
            //收集股票id
            var sidArr = [], sids;
            $('.sid').each(function() {
                sidArr.push($(this).html());
                sids = sidArr.join(',');
            });
            NB.apiAjax({
                type: 'GET',
                data: {"type": "price_list", "sids": sids},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/company/information/',
                success: function(data) {
                    //刷新价格
                    var valueCount = 0;     //总市值
                    $('.margin-record').each(function() {
                        var tr = $(this), item = data.data[tr.data('sid')], colorClassBk = colorClass = item['price_diff'] > 0 ? 'stock-up' : 'stock-under';
                        if (tr.children('.price').html() != item['price']) {    //价格变动才做变化
                            //现价
                            tr.children('.price').html(item['price']);
                            tr.children('.price').removeClass(stockColorClass).addClass(colorClass);

                            //涨跌幅
                            tr.children('.price-diff-rate').html(item['price_diff_rate'] + '%');
                            tr.children('.price-diff-rate').removeClass(stockColorClass).addClass(colorClass);

                            //市值
                            var itemValue = item['price'] * tr.children('.count').html();
                            tr.children('.market-value').html(itemValue.toFixed(2) * -1);
                            valueCount += itemValue;

                            //盈亏
                            var cost = tr.data('cost'), earn = itemValue - cost, colorClass = earn > 0 ? 'stock-up' : 'stock-under';
                            tr.children('.earn-rate').html((earn / cost * -1 * 100).toFixed(2) + '%');
                            tr.children('.earn-rate').removeClass(stockColorClass).addClass(colorClass);

                            tr.children('.earn').html(parseInt(earn));
                            tr.children('.earn').removeClass(stockColorClass).addClass(colorClass);

                            Stock.highlightField(tr.children('.hg-field'), colorClassBk);
                        } else {
                            valueCount += parseInt(tr.children('.market-value').html());
                        }
                    });
                    /*
                    var valueCountObj = $('#value_count');
                    if (valueCount != valueCountObj.html()) {
                        var colorClass = valueCount > valueCountObj.html() ? 'stock-up' : 'stock-under',
                            allProperty = valueCount + parseInt($('#money').html());

                        valueCountObj.html(valueCount);
                        $('#property').html(allProperty);

                        console.log(allProperty, valueCount);
                        //刷新仓位比例
                        $('#p_rate').html(parseInt(valueCount / allProperty * 100));

                        //刷新最近盈亏
                        $('.last-earn').each(function() {
                            $(this).html(parseInt(allProperty - $(this).data('earn')));
                            $(this).removeClass(stockColorClass).addClass($(this).html() >= 0 ? 'stock-up' : 'stock-under');
                        });
                        Stock.highlightField($('.p-field'), colorClass);
                    } */
                },
                error: function(data) {
                    if (data.code == 40301) {
                        location.reload();
                    }
                }
            });
        }
        refreshData(true);
        setInterval(function() { refreshData(false); }, 5000);
        
        //平仓按钮点击
        $('.close-btn').click(function() {
            var msrid = $(this).data('msrid'), tr = $('#msr_' + msrid);
            $('#close_msrid').val(msrid);
            $('#close_sname').html(tr.data('sid') + ' ' + tr.children('.sname').html());
            $('#close_price').val(tr.children('.price').html());
            $('#close_count').val(Math.abs(parseInt(tr.children('.count').html())));
            
            $('#close_modal').modal({backdrop: 'static'});
        });
        
        $('#trade_submit').click(function() {
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {msrid: $('#close_msrid').val(), price: $('#close_price').val(), count: $('#close_count').val()},
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
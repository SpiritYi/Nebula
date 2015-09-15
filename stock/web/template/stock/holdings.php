<style type="text/css">
    .sname {
        color: #A020F0;
    }
</style>
<div class="container">
    <div>
        <h3>总资产</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>总资产</th>
                    <th>股票市值</th>
                    <th>现金</th>
                    <th>可用现金</th>

                    <th>今日盈亏</th>
                    <th>本周盈亏</th>
                    <th>本月盈亏</th>
                    <th>今年盈亏</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="property" class="p-field">0</td>
                    <td id="value_count" class="p-field">0</td>
                    <td id="money"><?php echo (int)$this->userProperty['money']; ?></td>
                    <td><?php echo (int)$this->userProperty['usable_money']; ?></td>

                    <td class="last-earn p-field" data-earn="<?php echo $this->lastProperty['last_day']; ?>">0</td>
                    <td class="last-earn p-field" data-earn="<?php echo $this->lastProperty['last_week']; ?>">0</td>
                    <td class="last-earn p-field" data-earn="<?php echo $this->lastProperty['last_month']; ?>">0</td>
                    <td class="last-earn p-field" data-earn="<?php echo $this->lastProperty['last_year']; ?>">0</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <h3>持股</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>编号</th>
                    <th>股票代码</th>
                    <th>股票名称</th>
                    <th>总数量</th>
                    <th>可用数量</th>
                    <th>持仓成本</th>

                    <th>现价</th>
                    <th>涨跌幅</th>
                    <th>市值</th>

                    <th>盈亏率</th>
                    <th>浮动盈亏</th>

                    <th>止损提醒</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $index = 1;
                    foreach ($this->stockList as $item) { ?>
                        <tr id="<?php echo $item['sid']; ?>" data-cost="<?php echo $item['cost']; ?>">
                            <td class="note"><?php echo $index; ?></td>
                            <td class="sid"><?php echo $item['sid']; ?></td>
                            <td class="sname"><?php echo $item['sname']; ?></td>
                            <td class="count"><?php echo $item['count']; ?></td>
                            <td class=""><?php echo $item['available_count']; ?></td>
                            <td><?php echo $this->showPrice($item['per_cost'], 3); ?></td>

                            <td class="price hg-field"></td>
                            <td class="price-diff-rate hg-field"></td>
                            <td class="market-value"></td>

                            <td class="earn-rate hg-field"></td>
                            <td class="earn hg-field"></td>

                            <td class="note"><?php echo $this->showPrice($item['loss_limit']); ?></td>
                        </tr>
                    <?php $index ++;
                } ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB', 'Stock'], function(NB, Stock) {
        NB.navActive($('#navbar_holdings'));

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
                    $.each(data.data, function(sid, item) {
                        var tr = $('#' + sid), colorClassBk = colorClass = item['price_diff'] > 0 ? 'stock-up' : 'stock-under';
                        if (tr.children('.price').html() != item['price']) {    //价格变动才做变化
                            //现价
                            tr.children('.price').html(item['price']);
                            tr.children('.price').removeClass(stockColorClass).addClass(colorClass);

                            //涨跌幅
                            tr.children('.price-diff-rate').html(item['price_diff_rate'] + '%');
                            tr.children('.price-diff-rate').removeClass(stockColorClass).addClass(colorClass);

                            //市值
                            var itemValue = item['price'] * tr.children('.count').html();
                            tr.children('.market-value').html(itemValue);
                            valueCount += itemValue;

                            //盈亏
                            var cost = tr.data('cost'), earn = itemValue - cost, colorClass = earn > 0 ? 'stock-up' : 'stock-under';
                            tr.children('.earn-rate').html((earn / cost * 100).toFixed(2) + '%');
                            tr.children('.earn-rate').removeClass(stockColorClass).addClass(colorClass);

                            tr.children('.earn').html(parseInt(earn));
                            tr.children('.earn').removeClass(stockColorClass).addClass(colorClass);

                            Stock.highlightField(tr.children('.hg-field'), colorClassBk);
                        } else {
                            valueCount += parseInt(tr.children('.market-value').html());
                        }
                    });
                    if (valueCount != $('#value_count').html()) {
                        var colorClass = valueCount > $('#value_count').html() ? 'stock-up' : 'stock-under',
                            allProperty = valueCount + parseInt($('#money').html());

                        $('#value_count').html(valueCount);
                        $('#property').html(allProperty);

                        //刷新最近盈亏
                        $('.last-earn').each(function() {
                            $(this).html(parseInt(allProperty - $(this).data('earn')));
                            $(this).removeClass(stockColorClass).addClass($(this).html() >= 0 ? 'stock-up' : 'stock-under');
                        });
                        Stock.highlightField($('.p-field'), colorClass);
                    }
                }
            });
        }
        refreshData(true);
        setInterval(function() { refreshData(false); }, 5000);
    })
</script>

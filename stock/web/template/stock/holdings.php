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
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th id="property">0</th>
                    <th id="value_count">0</th>
                    <th id="money"><?php echo (int)$this->userProperty['money']; ?></th>
                    <th><?php echo (int)$this->userProperty['usable_money']; ?></th>
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
                            <td><?php echo $index; ?></td>
                            <td class="sid"><?php echo $item['sid']; ?></td>
                            <td><?php echo $item['sname']; ?></td>
                            <td class="count"><?php echo $item['count']; ?></td>
                            <td><?php echo $this->showPrice($item['per_cost'], 3); ?></td>

                            <td class="price"></td>
                            <td class="price-diff-rate"></td>
                            <td class="market-value"></td>

                            <td class="earn-rate"></td>
                            <td class="earn"></td>

                            <td><?php echo $this->showPrice($item['loss_limit']); ?></td>
                        </tr>
                    <?php $index ++;
                } ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#navbar_holdings'));

        //刷新页面报价信息
        var stockColorClass = 'stock-up stock-under';
        function refreshData() {
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
                        var tr = $('#' + sid), colorClass = item['price_diff'] > 0 ? 'stock-up' : 'stock-under';
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
                    });
                    //刷新总资产
                    $('#value_count').html(valueCount);
                    $('#property').html(valueCount + parseInt($('#money').html()));
                }
            });
        }
        refreshData();
    })
</script>

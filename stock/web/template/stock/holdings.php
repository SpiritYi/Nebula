<div class="container">
    <div>
        <h3>总资产</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>总资产</th>
                    <th>股票市值</th>
                    <th>可用现金</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>0</th>
                    <th>0</th>
                    <th><?php echo (int)$this->userProperty['money']; ?></th>
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
                    <th>止损提醒</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $index = 1;
                    foreach ($this->stockList as $item) { ?>
                        <tr>
                            <td><?php echo $index; ?></td>
                            <td><?php echo $item['sid']; ?></td>
                            <td><?php echo $item['sname']; ?></td>
                            <td><?php echo $item['count']; ?></td>
                            <td><?php echo $this->showPrice($item['per_cost'], 3); ?></td>
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
    })
</script>

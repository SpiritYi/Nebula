<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>编号</th>
                <th>股票代码</th>
                <th>股票名称</th>
                <th>总数量</th>
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
                        <td><?php echo sprintf('%.2f', $item['loss_limit']); ?></td>
                    </tr>
                <?php $index ++;
            } ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#navbar_holdings'));
    })
</script>

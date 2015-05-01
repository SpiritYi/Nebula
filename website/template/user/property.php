<div>
    <div class="page-header red">
        <h2><?php echo empty($this->userPropertyCount) ? 0 : number_format($this->userPropertyCount[0]['count']); ?></h2>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>日期</th>
                    <th class="text-right">份额</th>
                    <th>来源</th>
                    <th>说明</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->recordList as $item) { ?>
                    <tr>
                        <td><?php echo date('Y/m/d', $item['time']); ?></td>
                        <?php
                        $amountStr = number_format($item['amount']);
                        if ($item['type'] == PropertyRecordModel::TYPE_GAIN) {
                            echo '<td class="text-right red"><strong>' . $amountStr . '</strong></td>';
                        } else if ($item['type'] == PropertyRecordModel::TYPE_LOSS) {
                            echo '<td class="text-right green">' . $amountStr . '</td>';
                        } else {
                            echo '<td class="text-right">' . $amountStr . '</td>';
                        } ?>
                        <td><?php echo PropertyRecordModel::$TYPE_NAME[$item['type']]; ?></td>
                        <td><?php echo $item['notes']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#nav_u_property'));
    });
</script>

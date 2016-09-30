<style type="text/css">
    .submit-group img {
        margin-right: 15px;
    }
</style>
<div id="asset_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>外汇总值</h4>
            </div>
            <div class="modal-body row">
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="asset" />
                </div>
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="date_str" value="<?php echo date('Y/m/d ') . '9:15:00'; ?>" />
                </div>
            </div>
            <div class="modal-footer submit-group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <button type="button" id="save_submit" class="btn btn-primary">提 交</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB', 'Stock'], function(NB, Stock) {

        $('#asset_modal').on('show.bs.modal', function() {
            console.log('herere');
            NB.apiAjax({
                type: 'GET',
                data: {cat: 'latest_asset'},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/stock/forexassetbk',
                success: function(data) {
                    $('#asset').val(data.data.asset);
                }
            });
        });

        //提交记录
        $('#save_submit').click(function() {
            var assetV = $('#asset').val();
            if (assetV <= 0) {
                NB.alert('资产值不正确', 'danger');
                return;
            }

            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {asset: assetV, date_str: $('#date_str').val()},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/stock/forexassetbk',
                success: function(data) {
                    $('#asset').val('');
                    NB.alert(data.message, 'success');
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

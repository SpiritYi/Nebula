<style type="text/css">
    .submit-group img {
        margin-right: 15px;
    }
</style>
<div id="earnings_rate_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>添加营收数据</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="sr-only">类型</label>
                            <select id="earnings_type" class="selectpicker" data-width="100%">
                                <?php foreach ($this->rateTypeList as $key => $name) { ?>
                                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">百分比</span>
                                <input type="text" id="rate" class="form-control" placeholder="-99.99 ~ 99.99" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" id="date" class="form-control" value="<?php echo date('Y/m/10', strtotime('-1 month')); ?>" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer submit-group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <button type="button" id="rate_submit" class="btn btn-primary">提 交</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        $('#rate_submit').click(function() {
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {type: $('#earnings_type').val(), rate: $('#rate').val(), time: $('#date').val()},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/company/earningsbk/',
                success: function(data) {
                    NB.alert(data.message, 'success');
                    $('#rate').val('');
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

<style type="text/css">
    .submit-group img {
        margin-right: 15px;
    }
</style>
<div id="property_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>添加资产</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="sr-only">用户</label>
                            <select id="user_select" class="selectpicker" data-width="100%">
                                <?php foreach ($this->allUserList as $userInfo) { ?>
                                    <option value="<?php echo $userInfo['id']; ?>"><?php echo $userInfo['nickname']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="sr-only">类型</label>
                            <select id="type_select" class="selectpicker" data-width="100%">
                                <?php foreach ($this->propertyType as $typeId => $typeName) { ?>
                                    <option value="<?php echo $typeId; ?>"><?php echo $typeName; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">金额</span>
                                <input type="text" id="amount" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" id="time" class="form-control" value="<?php echo date('Y/m/d 10:00:00'); ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">说明</span>
                                <input type="text" id="notes" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer submit-group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <button type="button" id="property_submit" class="btn btn-primary">提 交</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        $('#property_submit').click(function() {
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {type: $('#type_select').val(), amount: $('#amount').val(), time: $('#time').val(), notes: $('#notes').val()},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/user/propertybk/' + $('#user_select').val() + '/',
                success: function(data) {
                    NB.alert(data.message, 'success');
                    $('#amount').val('');
                    $('#notes').val('');
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

<style type="text/css">
    .submit-group img {
        margin-right: 15px;
    }
</style>
<div id="article_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>发布文章</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="sr-only">类型</label>
                            <select id="article_type" class="selectpicker" data-width="100%">
                                <?php foreach ($this->articleTypeList as $key => $name) { ?>
                                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" id="p_time" class="form-control" value="<?php echo date('Y/m/d H:i:s'); ?>" />
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">标题</span>
                                <input type="text" id="title" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-12">
                            <textarea class="form-control" row="10" id="content" placeholder="简介"></textarea>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">页面文件</span>
                                <input type="text" id="template" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer submit-group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <button type="button" id="article_submit" class="btn btn-primary">提 交</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        $('#article_submit').click(function() {
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {type: $('#article_type').val(), title: $('#title').val(), content: $('#content').val(), template: $('#template').val(),
                     time: $('#p_time').val()
                },
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/company/articlebk/',
                success: function(data) {
                    NB.alert(data.message, 'success');
                    $('#title').val('');
                    $('#content').val('');
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

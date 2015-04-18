
<div class="container">
    <div class="paper">
        <textarea class="form-control" row="10" id="sug_content"></textarea>
        <div class="col-lg-3 col-lg-offset-9">
            <div class="input-group submit-group">
                <input type="button" id="modifyProfile" class="btn btn-default" value="发 送" />
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive('#navbar_about');

        $('#sug_content').focus();
        $('#sug_content').val("致星云财富管理层\n");

        $('.submit-group').click(function() {
            var content = $('#sug_content').val();

            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {'content': content},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/company/support/suggestion/',
                success: function(data) {
                    $('#sug_content').val("致星云财富管理层\n");
                    NB.alert(data.data, 'success');
                },
                error: function(data) {
                    NB.alertClose(data.message, 'danger');
                }
            });
        });
    });
</script>

<div class="container">
    <div class="col-lg-4">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-4 control-label">原密码</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="pwd_origin" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">新密码</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="new_pwd" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">新密码</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="new_pwd_again" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8 submit-group" >
                    <button type="button" class="btn btn-default" id="change_btn">提交</button>
                    <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->staExport('/script/lib/jquery.md5.js'); ?>
<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#navbar_account'));

        $('#change_btn').click(function() {
            var pwdOrigin = $.md5($('#pwd_origin').val()), newPwd = $.md5($('#new_pwd').val()), newPwdAgain = $.md5($('#new_pwd_again').val());

            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'PUT',
                data: {"pwd_origin": pwdOrigin, "new_pwd": newPwd, "new_pwd_again": newPwdAgain},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/account/password/',
                success: function(data) {
                    NB.alert(data.message, 'success');
                    $('input[type="password"]').val('');

                    setTimeout("$('#navbar_signout').click()", 2000);
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        })
    });
</script>

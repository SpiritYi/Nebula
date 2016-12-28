<style type="text/css">
    .submit-group img {
        margin: 0px;
    }
</style>
<div class="container" style="margin-top: 50px;">
    <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
            <h2>管理员登陆</h2>
        </div>
    </div>
    <div class="row">
        <div class="form-signin col-lg-4 col-lg-offset-4">
            <div class="input-group">
                <input type="password" id="username" class="form-control" placeholder="Identify..." >
                <span class="input-group-btn">
                    <button id="signin_btn" class="btn btn-default" type="button">Sign In</button>
                </span>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="submit-group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        //回车支持
        $('#username').keyup(function(e) {
            if (e.keyCode == 13) {
                $('#signin_btn').click();
            }
        });

        $('#signin_btn').click(function() {
            var username = $('#username').val();

            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {"username": username, "admin_type": 1},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/user/session/',
                datatype: 'json',
                success: function(data) {
                    var cookieTime = new Date(data.data.cookie.t_h);
                    $.cookie(data.data.cookie.k, data.data.cookie.v, {expires: cookieTime, path: '/'});
                    location.reload();
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

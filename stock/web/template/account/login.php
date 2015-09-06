<style type="text/css">
    .form-signin {
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
    }
    .form-signin .form-control {
        padding: 10px;
        height: auto
    }
    .form-signin input[type="username"] {
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
        border-top-right-radius: 0;
        border-top-left-radius: 0;
        border-top-width: 0;
    }
    .form-signin #signin_btn {
        margin: 15px 0px;
    }
    .submit-group {
        text-align: center;
    }
    .submit-group img {
        margin: 0px;
    }
</style>
<div class="container" style="margin-top: 50px;">
    <div class="form-signin">
        <h2>用户登录</h2>
        <input type="username" id="username" class="form-control" placeholder="用户名" value="" >
        <input type="password" id="password" class="form-control" placeholder="密码">
        <!-- <input type="password" id="password" class="form-control" placeholder="密码" value="" > -->
        <button id="signin_btn" class="btn btn-primary btn-block" type="button">登录</button>
        <div class="submit-group">
            <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
        </div>
    </div>

    <div class="col-lg-4">

    </div>
</div>

<?php $this->staExport('/script/lib/jquery.md5.js'); ?>
<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        //回车支持
        $('#password').keyup(function(e) {
            if (e.keyCode == 13) {
                $('#signin_btn').click();
            }
        });

        $('#signin_btn').click(function() {
            var username = $('#username').val();
            var pwd = $('#password').val();
            if (!pwd) {
                NB.alert('密码不能为空', 'danger');
                return;
            }
            var token = $.md5(pwd), date = new Date(), password = $.md5(date.getMilliseconds());

            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {"username": username, "token": token, "password": password},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/account/session/',
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

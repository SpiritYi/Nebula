<style type="text/css">
    .modify-form div {
        margin-bottom: 15px;
        width: 300px;
    }
    .modify-form div span {
        width: 80px;
    }
</style>
<div class="row">
    <div class="col-lg-5 modify-form">
        <div class="input-group">
          <span class="input-group-addon" id="sizing-addon2">称呼</span>
          <input type="text" class="form-control" id="nickname" aria-describedby="sizing-addon2" value="<?php echo $this->myInfo['nickname']; ?>">
        </div>

        <div class="input-group">
          <span class="input-group-addon" id="sizing-addon2">Email</span>
          <input type="text" class="form-control" id="email" aria-describedby="sizing-addon2" value="<?php echo $this->myInfo['email']; ?>">
        </div>

        <div class="input-group">
          <span class="input-group-addon" id="sizing-addon2">Phone</span>
          <input type="text" class="form-control" id="phone" aria-describedby="sizing-addon2" value="<?php echo $this->myInfo['phone']; ?>">
        </div>

        <div class="input-group submit-group">
            <input type="button" id="modifyProfile" class="btn btn-default" data-uid="<?php echo $this->myInfo['id']; ?>" value="提 交" />
            <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB', 'script/base/nbconfig.js'], function(NB, config) {
        NB.navActive('#nav_u_profile');

        // $('.submit-group img')
        $('#modifyProfile').click(function() {
            $('.submit-group img').show();
            var uid = $(this).data('uid');

            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'PUT',
                data: {'nickname': $('#nickname').val(), 'email': $('#email').val(), 'phone': $('#phone').val()},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/user/profile/' + uid + '/',
                datatype: 'json',
                success: function(data) {
                    $('#navbar_user a').html($('#nickname').val());
                    NB.alert(data.message, 'success');
                }, error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

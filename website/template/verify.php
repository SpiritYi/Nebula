<div class="footer">
    <!-- <div class="explain">On Cooking ...</div> -->
    <div class="container" style="margin-top: 5px;">
        <div class="col-lg-4 col-lg-offset-4">
            <div class="input-group">
              <input type="password" id="username" class="form-control" placeholder="Identify ...">
              <span class="input-group-btn">
                <button class="btn btn-default" id="login_btn" type="button">&emsp;Fire&emsp;</button>
              </span>
            </div><!-- /input-group -->
        </div>
        <div class="col-lg-4" id="login_process" style="display: none;">
            <div class="progress" style="margin: 6px 0px 0px 0px;">
              <div id='login_bar' class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="background-color: #A020F0; width: 0%;">
              </div>
            </div>
        </div>
    </div>
    <div class="container" style="margin-top: 20px;">
        <div class="copyright" data-ceo="SpiritYi">© <?php echo date('Y'); ?> Nebula Investment Fund. All Rights Reserved.</div>
    </div>
</div>

<script text="javascript/text">
    seajs.use(['NB'], function(NB) {
        //回车支持
        $('#username').keyup(function(e) {
            if (e.keyCode == 13) {
                $('#login_btn').click();
            }
        });
        $('#login_btn').click(function() {
            var barWidth = 0, runFlag = true, process = $('#login_process'), bar = $('#login_bar');
            process.show();
            bar.width(0);
            setInterval(function() {
                if (runFlag && barWidth < 110) {
                    bar.css({'width': barWidth + '%'});
                }
                barWidth = barWidth + 2;
            }, 100);

            var username = $('#username').val();
            NB.apiAjax({
                type: 'POST',
                data: {'username': username},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/user/session/',
                datatype: 'json',
                success: function(data) {
                    var cookieTime = new Date(data.data.cookie.t_h);
                    $.cookie(data.data.cookie.k, data.data.cookie.v, {expires: cookieTime, path: '/'});
                    location.reload();
                },
                error: function(data) {
                    runFlag = false;
                    process.hide();
                    bar.css({'width': 0});

                    var error = '请求出错! ';
                    try {
                        res = $.parseJSON(data.responseText);
                        error = error + res.message;
                    } catch (e) {}
                    NB.alert(error, 'danger');
                }
            });
        });
    });
</script>


<?php echo $this->staExport('/css/stock/user/message.css'); ?>
<?php echo $this->staExport('/css/lib/icheck/minimal.css'); ?>

<div class="container-fluid">
    <div class="row" id="row_main">
        <div class="clock">
            <p id="tinker"></p>
        </div>
    </div>
    <div class="noticebar">
        <ul class="msg-flow">
            <!--
            <li class="msg-item" id="item_11">
                <div class="row">
                    <div class="col-sm-2 check-col">
                        <input type="checkbox" class="read_check" data-id="11">
                    </div>
                    <div class="col-sm-10 content-col">
                        <div class="head">
                            <span class="tt">test xiaoxi, test xiaoxisdf</span>
                            <span class="tm">10:30:12</span>
                        </div>
                        <div class="content">
                            让业主充,们自古就有靠水而居,充分展现自
                        </div>
                    </div>
                </div>
            </li>
            -->
        </ul>
    </div>
</div>

<!-- https://github.com/fronteed/iCheck -->
<?php echo $this->staExport('/script/lib/icheck.min.js'); ?>
<script type="text/javascript">
    seajs.use(['NB'], function(NB) {

        //选择框初始化
        function checkInit() {
            $('input').iCheck({
                checkboxClass: 'icheckbox_minimal',
                // radioClass: 'iradio_minimal',
                increaseArea: '20%' // optional
            });
            //勾选已读
            $('.read_check').on('ifChecked', function(event) {
                var mid = $(this).data('id');

                NB.apiAjax({
                    type: 'PUT',
                    url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/user/message/' + mid + '/',
                    success: function() {
                        var item = $('#item_' + mid);
                        item.find('.head .tt').addClass('read');
                        item.find('.content').addClass('read');
                        item.find('.read_check').iCheck('disable');

                        //减未读数
                        setTitleRemind($('title').data('unread_count') - 1);
                    },
                    error: function(data) {
                        NB.alert(data.message);
                    }
                });
            });
        }

        $(document).keydown(function(e) {
            if (e.keyCode == 79) {  //字母键o, open
                $('.noticebar').toggleClass('noticebar-hidden');
            }
        });

        //更新时间
        function timeRefresh() {
            var time = moment().format('HH:mm:ss'), tinker = $('#tinker');
            tinker.html(time);
        }
        timeRefresh();
        setInterval(timeRefresh, 1000);

        //设置未读消息提示
        function setTitleRemind(count) {
            if (count > 0) {
                $('title').html('(' + count + ') Bell');
                $('title').data('unread_count', count);
            } else {
                $('title').html('Tinker');
                $('title').data('unread_count', 0);
            }
            
        }
        function msgRefresh() {
            var midArr = [], mids = '';
            $('.read_check').each(function() {
                midArr.push($(this).data('id'));
            });
            mids = midArr.join(',');

            NB.apiAjax({
                type: "GET",
                data: {"classify": "unread", "mids": mids},
                url : '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/user/message/',
                success: function(data) {
                    //处理新消息
                    var html = '';
                    $.each(data.data.new_list, function(k, msg) {
                        var sendMoment = moment.unix(msg.send_time);
                        html += '<li class="msg-item" id="item_' + msg.id + '">\
                            <div class="row">\
                                <div class="col-sm-2 check-col">\
                                    <input type="checkbox" class="read_check" data-id="' + msg.id + '">\
                                </div>\
                                <div class="col-sm-10 content-col">\
                                    <div class="head">\
                                        <span class="tt">' + msg.title.substr(0, 20) + '</span>\
                                        <span class="tm" title="' + sendMoment.format('YYYY/MM/DD')+ '">' + sendMoment.format('HH:mm:ss') + '</span>\
                                    </div>\
                                    <div class="content">' + msg.content + '</div>\
                                </div>\
                            </div>\
                        </li>';
                    });
                    $('.msg-flow').prepend(html);
                    checkInit();

                    //处理已读
                    $.each(data.data.read_ids, function(k, id) {
                        $('#item_' + id).find('.read_check').iCheck('check');
                    });

                    //消息提醒
                    setTitleRemind(data.data.unread_count);
                },
                error: function(data) {
                    if (data.code == 40301) {
                        location.reload();
                    }
                }
            });
        }
        msgRefresh();
        //自动刷新消息
        setInterval(msgRefresh, 5000);
    });
</script>
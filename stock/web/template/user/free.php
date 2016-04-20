<style type="text/css">
    .event-item {
        padding: 20px 20px 10px 20px;
        margin: 20px 0px;
        background-color: #F5F5F5;
    }
    .top-text {
        color: #BBB;
    }
    .big-word {
        font-size: 300%;
        color: #CCC;
    }
    .remain-day {
        float: left;
        width: 80px;
    }
    .gap-text {
        margin-right: 20px;
    }
</style>
<div class="container">
    <div class="row">
        <?php foreach($this->endTimeConfig as $i => $endTimeStr) {
            if (strtotime($endTimeStr) < time()) {
                continue;
            } ?>
            <div class="event-item">
                <div class="top-text">
                    <span>距离 <?php echo $endTimeStr; ?></span>
                </div>
                <div class="time-body">
                    <input type="hidden" class="end-ts" data-tag="<?php echo $i; ?>" value="<?php echo strtotime($endTimeStr); ?>" />
                    <span class="big-word remain-day"></span><span class="gap-text">天</span>
                    <span class="big-word tail-time"></span>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">

    Object.defineProperty(Object.prototype, 'twoDigit', {
        value: function() {
            if (this.toString().length == 1) {
                return '0' + this.toString();
            }
            return this.toString();
        },
        enumerable: false
    });

    seajs.use(['NB'], function(NB) {
        var nowTstamp = parseInt(new Date().getTime() / 1000), daySec = 24 * 3600;
        
        var itemCount = $('.event-item').length;

        //初始化各项结束
        var secArr = new Array(itemCount);
        function initTimeItem() {
            $('.event-item').each(function() {
                var index = $(this).find('.time-body .end-ts').data('tag');
                secArr[index] = $(this).find('.time-body .end-ts').val() - nowTstamp;
            });
        }
        initTimeItem();

        //刷新时间
        function refreshTime(jqObj) {
            var index = jqObj.find('.time-body .end-ts').data('tag');
            var sec = secArr[index];
            var remDay = parseInt(sec / (24 * 3600));
            var dayTailSec = sec % daySec;
            var hour = parseInt(dayTailSec / 3600);
            var minute = parseInt(dayTailSec % 3600 / 60);
            var second = parseInt(dayTailSec % 60);
            var timeStr = hour.twoDigit() + ':' + minute.twoDigit() + ':' + second.twoDigit();

            jqObj.find('.tail-time').html(timeStr);
            jqObj.find('.remain-day').html(remDay);
            secArr[index] --;

            if (secArr[index] % 60 == 0) {    //每分钟校正时间
                secArr[index] = jqObj.find('.time-body .end-ts').val() - parseInt(new Date().getTime() / 1000);
            }
        }
        function betachRun() {
            $('.event-item').each(function() {
               refreshTime($(this));
            })
        }
        betachRun();
        setInterval(betachRun, 1000);

    });
</script>
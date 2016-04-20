<style type="text/css">
    .top-text {
        margin: 30px 0 5px 0;
        color: #CCC;
    }
    .big-word {
        font-size: 300%;
        color: #CCC;
    }
    .gap-text {
        margin-right: 20px;
    }
</style>
<div class="container">
    <div class="row">
        <div class="top-text">
            <span>距离 2017/12/31</span>
        </div>
        <div>
            <input type="hidden" id="end_ts" value="<?php echo $this->endTstamp; ?>" />
            <span class="big-word" id="remain_day"></span><span class="gap-text">天</span>
            <span class="big-word" id="tail_time"></span>
        </div>
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

        var endTs = $('#end_ts').val();
        var nowTstamp = parseInt(new Date().getTime() / 1000), daySec = 24 * 3600;
        var sec = endTs - nowTstamp;
        function refreshTime() {
            var remDay = parseInt(sec / (24 * 3600));
            var dayTailSec = sec % daySec;
            var hour = parseInt(dayTailSec / 3600);
            var minute = parseInt(dayTailSec % 3600 / 60);
            var second = parseInt(dayTailSec % 60);
            var timeStr = hour.twoDigit() + ':' + minute.twoDigit() + ':' + second.twoDigit();

            $('#tail_time').html(timeStr);
            $('#remain_day').html(remDay);
            sec --;

            if (sec % 60 == 0) {    //每分钟校正时间
                sec = endTs - parseInt(new Date().getTime() / 1000);
                console.log(sec);
            }
        }
        refreshTime();
        setInterval(refreshTime, 1000);

    });
</script>
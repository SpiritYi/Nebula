<style type="text/css">
    .clock {
        color: #DDDDDD;
    }
    .clock p {
        margin-top: 100px;
        font-size: 128px;
        text-align: center;
    }
</style>
<div class="container">
    <div class="clock">
        <p id="tinker"></p>
    </div>
</div>

<script type="text/javascript">
    seajs.use([''], function() {

        //更新时间
        function colorRand() {
            return Math.floor(Math.random() * 255);
        }
        var r = colorRand(), g = colorRand(), b = colorRand();
        function timeRefresh() {
            var time = moment().format('HH:mm:ss'), tinker = $('#tinker');
            tinker.html(time);
            // r ++; g ++; b ++;
            // tinker.css('color', 'rgb(' + r % 255 + ', ' + g % 256 + ', ' + b % 256 + ')');
        }
        timeRefresh();
        setInterval(timeRefresh, 1000);
    });
</script>

<style type="text/css">
    .res_block {
        height: 250px;
    }
    .res_block p {
        float: left;
        padding-left: 25px;
        width: 100%;
        font-size: 110%;
    }
    .rb-rate {
        float: left;
        width: 70px;
    }
    .rb-amount {
        float: left;
        width: 50px;
        text-align: right;
    }
</style>
<div id="property_rate_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>资产比例计算</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">总额</span>
                                <input type="text" id="property_sum" class="form-control" placeholder="100000" value="179038" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="res_block" id="rate_block">
                                <!-- <p>80%  188000</p>
                                <p>80%  188000</p>
                                <p>80%  188000</p> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {

        $('#property_sum').keyup(function(e) {
            if (e.keyCode == 13) {
                var sum = $(this).val();
                var html = '';
                for (var i = 1; i > 0.2; i -= 0.1) {
                    var temp = parseInt(sum * i % 10000) + 10000;
                    html += '<p class="note"><span class="rb-rate">' + parseInt(i * 100) + '%</span>' +
                        '<span class="rb-amount">' + parseInt(sum * i / 10000) + '&nbsp;</span>' +
                        '<span>' + temp.toString().substring(1) + '</span></p>';
                }
                $('#rate_block').html(html);
            }
        });
    });
</script>

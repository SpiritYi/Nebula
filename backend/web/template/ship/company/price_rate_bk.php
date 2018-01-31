<style type="text/css">
    #result {
        margin-top:15px;
    }
    #result tbody tr td {
        padding:12px 0px;
    }
</style>

<div id="price_rate_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>价格角度比例计算</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">价格</span>
                                <input type="text" id="price_str" class="form-control" placeholder="100000" value="12.80" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="result" class="table table-striped">
                                <thead></thead>
                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {

        $('#price_str').keyup(function(e) {
            if (e.keyCode == 13) {
                NB.apiAjax({
                    //            loading: $('.submit-group img'),
                    type: 'GET',
                    data: {price: $('#price_str').val()},
                    url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/company/ToolBk/price',
                    success: function (data) {
                        var html = '';
                        $.each(data.data, function (k, item) {
                            html += '<tr><td>' + item.fraction + '</td>' +
                                '<td>' + item.percent + '</td>' +
                                '<td class="red">' + item.end_price + '</td>'  +
                                '<td>' + item.spread + '</td></tr>'
                        });
                        $('#result tbody').html(html);
                    },
                    error: function (data) {
                        NB.alert(data.message, 'danger');
                    }
                });
            }
        });
    });
</script>
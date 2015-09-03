<style type="text/css">
    .submit img {
        margin-left: 15px;
    }
</style>
<div class="container">
    <div class="col-lg-4">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-4 control-label">上市公司</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="stockname" data-provide="typeahead">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">止损价格</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="price">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8 submit" >
                    <button type="button" class="btn btn-default" id="setlimit">提交</button>
                    <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {

        var companyObj;
        $('#stockname').typeahead({
            source: function(query, process) {
                console.log(query);
                NB.apiAjax({
                    type: 'GET',
                    data: {"type": "suggestion", "query": query},
                    url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/company/information/',
                    success: function(data) {
                        companyObj = data.data.obj;
                        process(data.data.show);
                    },
                    error: function(data) {
                        NB.alert(data.message, 'danger');
                    }
                });
            },
            updater: function(str) {    //选择之后更新id
                var strArr = str.split(' '), info = companyObj[strArr[0]];
                $('#stockname').data('sid', info['sid']);
                return info['sname'];
            }
        });

        $('#setlimit').click(function() {
            var sid = $('#stockname').data('sid'), price = $('#price').val();

            NB.apiAjax({
                loading: $('.submit img'),
                type: 'PUT',
                data: {"price":price},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/user/stock/' + sid + '/losslimit/',
                success: function(data) {
                    $('#price').val('');
                    NB.alert(data.message, 'success');
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            })
        });
    })
</script>

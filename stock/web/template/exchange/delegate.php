<style type="text/css">
    .dlgt-direction {
        margin-bottom: 22px;
    }
    .form-group label, #op_count {
        margin-top: 8px;
    }
    .sc {
        position: relative;
    }
    .sc .dropdown-menu {
        width: 100%;
    }
    .delegate-block {
        background-color: #F5F5F5;
    }
    .delegate-block .table {
        margin-bottom: 10px;
    }
    .delegate-block p.title {
        margin-top: 10px;
        padding-left: 8px;
        color: #D0D0D0;
        font-size: 105%;
    }
    .delegate-block .row {
        padding: 5px 0px;
    }
    .delegate-block .table a:hover {
        cursor: pointer;
    }
</style>
<div class="container">
    <ul class="nav nav-tabs dlgt-direction">
        <li role="presentation" class="<?php echo $this->op == DelegatePage::OP_BUY ? 'active' : ''; ?>"><a href="/exchange/delegate?op=buy">委买</a></li>
        <li role="presentation" class="<?php echo $this->op == DelegatePage::OP_SELL ? 'active' : ''; ?>"><a href="/exchange/delegate?op=sell">委卖</a></li>
    </ul>
</div>
<div class="container">
    <div class="col-lg-4">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3">公司</label>
                <div class="col-sm-8">
                    <div class="sc">
                        <?php if ($this->op == DelegatePage::OP_SELL) { ?>
                            <select class="selectpicker" data-width="100%" id="stock_slt_name">
                                <?php foreach ($this->userStockList as $item) { ?>
                                    <option value="<?php echo $item['sid']; ?>" data-available_count="<?php echo $item['available_count']; ?>"><?php echo $item['sname'] . ' ' . $item['sid']; ?></option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <input type="text" class="form-control" id="stockname" data-sid="" data-provide="typeahead">
                        <?php } ?>
                        <input type="hidden" id="dlgt_sid" value="" />
                        <input type="hidden" id="dlgt_direction" value="<?php echo $this->op == DelegatePage::OP_SELL ? -1 : 1; ?>" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">报价方式</label>
                <div class="col-sm-8">
                    <select class="selectpicker" data-width="100%" id="limitStatus">
                        <option value="1">自定义价格</option>
                        <option value="2">现价</option>
                    </select>
                </div>
            </div>
            <div class="form-group" id="priceItem">
                <label class="col-sm-3">价格</label>
                <div class="col-sm-8">
                    <input type="text" id="price" class="form-control" data-usable-money="<?php echo $this->userInfo['usable_money']; ?>" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3"><?php echo $this->op == DelegatePage::OP_SELL ? '可卖数量' : '可买数量'; ?></label>
                <div class="col-sm-8">
                    <div id="op_count"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">数量</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="count" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-8 submit-group">
                    <button type="button" class="btn btn-default" id="addDelegate">提交</button>
                    <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
    </div>
    <div class="col-lg-4 delegate-block">
        <p class="title"><?php echo $this->op == DelegatePage::OP_SELL ? '委卖列表' : '委买列表'; ?></p>
        <table class="table" id="dgt_table" style="display:">
            <!-- <tr id="table_tr_1">
                <td>
                    <div class="row">
                        <div class="col-sm-6"><strong>金鸿能源</strong><em style="margin-left: 10px; color: #D0D0D0;">600829</em></div>
                        <div class="col-sm-6 text-right"><a class="cancel-delegate" data-tr="1">撤销</a></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">12.87</div>
                        <div class="col-sm-4">1000</div>
                        <div class="col-sm-4 text-right" style="color: #D0D0D0">08/08 22:10</div>
                    </div>
                </td>
            </tr> -->
        </table>
    </div>
</div>

<?php $this->staExport('/script/lib/moment.min.js'); ?>
<script type="text/javascript">
    seajs.use(['NB', 'Stock'], function(NB, Stock) {
        NB.navActive($('#navbar_delegate'));

        //用户搜索股票
        Stock.initStockSelect({
            selector: $('#stockname'),
            updaterBack: function() {
                var sid = $('#stockname').data('sid');
                NB.apiAjax({
                    type: 'GET',
                    url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/company/information/market/' + sid + '/',
                    success: function(data) {
                        $('#dlgt_sid').val(data.data.sid);
                        $('#price').val(data.data.price);
                        $('#op_count').html(Math.floor($('#price').data('usable-money') / data.data.price / 100) * 100);

                        // var priceDom = $('#tip_price');
                        // priceDom.html(data.data.price);
                        // //表示涨跌颜色
                        // if (data.data.price > data.data.ysd_closing_price) {
                        //     priceDom.addClass('stock-up');
                        // } else if (data.data.price < data.data.ysd_closing_price) {
                        //     priceDom.addClass('stock-under');
                        // }
                    }
                })
            }
        });

        $('#stock_slt_name').change(function() {
            var sid = $(this).val();
            $('#dlgt_sid').val(sid);
            $('#op_count').html($(this).find('option:selected').data('available_count'));
            //更新股票价格
            NB.apiAjax({
                type: 'GET',
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/company/information/market/' + sid + '/',
                success: function(data) {
                    $('#price').val(data.data.price);

                    // var priceDom = $('#tip_price');
                    // priceDom.html(data.data.price);
                    // //表示涨跌颜色
                    // if (data.data.price > data.data.ysd_closing_price) {
                    //     priceDom.addClass('stock-up');
                    // } else if (data.data.price < data.data.ysd_closing_price) {
                    //     priceDom.addClass('stock-under');
                    // }
                }
            })
        });
        $('#stock_slt_name').change();

        //价格方式
        $('#limitStatus').change(function() {
            var limitStatus = $(this).val();
            if (limitStatus == 1) {
                $('#price').val('');
                $('#priceItem').show();
            } else if (limitStatus == 2) {
                $('#price').val(-1);
                $('#priceItem').hide();
            }
        });

        //提交委托
        $('#addDelegate').click(function() {
            var direction = $('#dlgt_direction').val(), sid = $('#dlgt_sid').val(), price = $('#price').val(), count = $('#count').val();
            if (price <= 0) {
                NB.alert('价格不正确', 'danger'); return;
            }
            if (count <= 0) {
                NB.alert('数量不正确', 'danger'); return;
            }
            if (count % 100 != 0) {
                NB.alert('数量必须为100股整数倍', 'danger'); return;
            }
            $(this).attr('disabled', true);

            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'POST',
                data: {"direction": direction, "sid": sid, "price": price, "count": count},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/exchange/delegate/',
                success: function(data) {
                    loadDelegateList();     //刷新委托列表
                    $('#addDelegate').attr('disabled', false);
                    $('#count').val('');
                    NB.alert(data.message, 'success');
                },
                error: function(data) {
                    $('#addDelegate').attr('disabled', false);
                    NB.alert(data.message, 'danger');
                }
            });
        });

        loadDelegateList();
        //异步刷新委托列表
        function loadDelegateList() {
            NB.apiAjax({
                type:'GET',
                data: {'direction': $('#dlgt_direction').val()},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/exchange/delegate/',
                success: function(data) {
                    var html = '';
                    $.each(data.data, function(i, item) {
                        var dlgtDate = moment.unix(item['time']);
                        html += '<tr id="table_tr_' + item['did'] + '">' +
                            '<td>' +
                                '<div class="row">' +
                                    '<div class="col-sm-6"><strong>' + item['sname'] + '</strong><em style="margin-left: 10px; color: #D0D0D0;">' + item['sid'] + '</em></div>' +
                                    '<div class="col-sm-6 text-right">' + 
                                        '<a class="cancel-delegate" data-tr="' + item['did'] + '" data-did="' + item['did'] + '">撤销</a>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row">' +
                                    '<div class="col-sm-4">' + item['price'] + '</div>' +
                                    '<div class="col-sm-4">' + item['count'] + '</div>' +
                                    '<div class="col-sm-4 text-right" style="color: #D0D0D0">' + dlgtDate.format('MM/DD HH:mm') + '</div>' +
                                '</div>' +
                            '</td>';
                    });
                    $('#dgt_table').html(html);
                    $('#dgt_table').show();
                },
                error: function(data) {
                    NB.alert('加载委托列表数据出错', 'danger');
                }
            })
        }
        $('.cancel-delegate').addClass('disabled');

        //撤销委托
        var cancelFlag = true;          //防止并发
        $('table').on('click', 'a.cancel-delegate', function() {
            if (!cancelFlag) {
                NB.alert('其他撤销正在处理', 'danger');
            }
            cancelFlag = false;

            var trId = $(this).data('tr'), did = $(this).data('did');
            $('#table_tr_' + trId).hide();

            NB.apiAjax({
                type: 'DELETE',
                data: {},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/exchange/delegate/' + did + '/',
                success: function(data) {
                    cancelFlag = true;
                    NB.alert(data.message, 'success');
                },
                error: function(data) {
                    cancelFlag = true;
                    $('#table_tr_' + trId).show();
                    NB.alert(data.message, 'danger');
                }
            });
        });
    });
</script>

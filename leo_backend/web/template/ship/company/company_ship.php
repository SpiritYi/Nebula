<style type="text/css">
    .list-container {
        margin-top: 20px;
        padding: 15px;
        border-top: 1px solid #EEE;
    }
    tr.hover {
        cursor: pointer;
        color: #428BCA;
    }
    .info_board .column {
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #EEE;
    }
    .info_board h4 {
        color: #CBCBCB;
    }
</style>

<div class="row">
    <div class="col-lg-2">
        <input type="button" id="company_add_modal_btn" class="btn btn-default" value="添加公司" />
    </div>
</div>
<div class="row list-container">
    <div class="">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>公司编号</th>
                    <th>名称</th>
                    <th style="width: 150px;">客服电话</th>
                    <th>官方网站</th>
                    <th>公司地址</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->companyList as $item) { ?>
                    <tr data-cid="<?php echo $item['cid']; ?>">
                        <td id="cmp_source_<?php echo $item['cid']; ?>" style="display: none"><?php echo json_encode($item); ?></td>
                        <td class="cmp-item"><?php echo $item['cid']; ?></td>
                        <td class="cmp-item cmp-name"><?php echo $item['name']; ?></td>
                        <td class="cmp-item cmp-phone"><?php echo $item['phone']; ?></td>
                        <td class="cmp-item cmp-website"><?php echo $item['website']; ?></td>
                        <td class="cmp-item cmp-address"><?php echo $item['address']; ?></td>
                        <td>
                            <button type="button" class="btn btn-info btn-xs edit-btn" data-cid="<?php echo $item['cid']; ?>">编辑</button>
                            <button type="button" class="btn btn-danger btn-xs delete-btn" data-cid="<?php echo $item['cid']; ?>">删除</button>
                        </td>
                    </tr>
                <?php } ?>
                <!--- <tr>
                    <td>12</td>
                    <td>Abcam</td>
                    <td>132 8298 7627</td>
                    <td>http://www.abcam.com</td>
                    <td>上海浦东</td>
                    <td>编辑  删除</td>
                </tr> --->
            </tbody>
        </table>
    </div>
</div>

<div id="company_edit_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">company_info
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 id="modal_title">添加供应商公司</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">公司名称</span>
                                <input type="text" id="company_name" class="form-control" placeholder="" />
                                <input type="hidden" id="company_id" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">官方网站</span>
                                <input type="text" id="company_site" class="form-control" placeholder="" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">客服电话</span>
                                <input type="text" id="company_phone" class="form-control" placeholder="" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">公司地址</span>
                                <input type="text" id="company_address" class="form-control" placeholder="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer submit-group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <button type="button" id="info_submit" class="btn btn-primary">提 交</button>
            </div>
        </div>
    </div>
</div>

<div id="company_info_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">company_info
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 id="info_title">公司信息</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row info_board" style="margin-bottom: 15px;">
                        <div class="col-lg-8 column">
                            <h4>名称</h4>
                            <div id="info_name"></div>
                            <input type="hidden" id="info_cid" />
                        </div>
                        <div class="col-lg-8 column">
                            <h4>客服电话</h4>
                            <div id="info_phone"></div>
                        </div>
                        <div class="col-lg-8 column">
                            <h4>官方网站</h4>
                            <div id="info_site"></div>
                        </div>
                        <div class="col-lg-8">
                            <h4>公司地址</h4>
                            <div id="info_address"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="delete_group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <button type="button" id="info_delete" class="btn btn-danger">删 除</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#nav_companyship'));

        $('#company_add_modal_btn').click(function() {
            var companyInfo = [];
            companyInfo['modal_title'] = '添加供应商公司';
            initEditModal(companyInfo);

            $('#company_edit_modal').modal({backdrop: 'static'});
        });

        //表格栏点击
        $('.cmp-item').hover(
            function() {
                $(this).parent().addClass('hover');
            }, function() {
                $(this).parent().removeClass('hover');
            }
        ).click(function() {
            var cid = $(this).parent().data('cid'), trSourceSlt = '#cmp_source_' + cid;
            var itemArr = JSON.parse($(trSourceSlt).html());
            itemArr.title = '公司信息';

            initInfoModal(itemArr);
            $('#delete_group').hide();

            $('#company_info_modal').modal();
        });

        //初始化公司信息框
        function initInfoModal(info) {
            $('#info_title').html(info.title);
            $('#info_cid').val(info.cid);
            $('#info_name').html(info.name != undefined ? info.name : '(空)');
            $('#info_phone').html(info.phone != undefined ? info.phone : '(空)');
            $('#info_site').html(info.website != undefined ? info.website : '(空)');
            $('#info_address').html(info.address != undefined ? info.address : '(空)');
        }

        //资料提交按钮点击
        $('#info_submit').click(function() {
            var cid = $('#company_id').val();
            var method = 'POST', reqUrl = '<?php echo DomainConfig::API_DOMAIN; ?>' + '/leo/backend/company/leocompanybk/';
            if (cid) {      //添加数据
                method = 'PUT';
                reqUrl += cid + '/';
            }
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: method,     //更新或者新创建
                data: {
                    cid: cid,
                    name: $('#company_name').val(),
                    phone: $('#company_phone').val(),
                    address: $('#company_address').val(),
                    website: $('#company_site').val()
                },
                url: reqUrl,
                success: function(data) {
                    NB.alert(data.message, 'success');

                    setTimeout("self.location.reload()", 1000);
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });

        });

        //编辑按钮点击
        $('.edit-btn').click(function() {
            var cid = $(this).data('cid'), trSourceSlt = '#cmp_source_' + cid;
            var itemArr = JSON.parse($(trSourceSlt).html());

            var companyInfo = [];
            companyInfo['modal_title'] = '编辑供应商公司';
            companyInfo['cid'] = cid;
            companyInfo['name'] = itemArr.name;
            companyInfo['phone'] = itemArr.phone;
            companyInfo['website'] = itemArr.website;
            companyInfo['address'] = itemArr.address;
            initEditModal(companyInfo);

            $('#company_edit_modal').modal({backdrop: 'static'});
        });

        //点击删除, 弹出删除确认框
        $('.delete-btn').click(function() {
            var cid = $(this).data('cid'), trSourceSlt = '#cmp_source_' + cid;
            var itemArr = JSON.parse($(trSourceSlt).html());
            itemArr.title = '删除公司信息';

            initInfoModal(itemArr);
            $('#delete_group').show();

            $('#company_info_modal').modal();
        });
        //确认删除
        $('#info_delete').click(function() {
            var cid = $('#info_cid').val();
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'DELETE',     //删除
                data: {},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/leo/backend/company/leocompanybk/' + cid + '/',
                success: function(data) {
                    NB.alert(data.message, 'success');

                    setTimeout("self.location.reload()", 1000);
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });

        //初始化公司信息编辑框
        function initEditModal(info) {
            $('#company_id').val(info['cid'] != undefined ? info['cid'] : '');
            $('#modal_title').html(info['modal_title'] != undefined ? info['modal_title'] : '');
            $('#company_name').val(info['name'] != undefined ? info['name'] : '');
            $('#company_phone').val(info['phone'] != undefined ? info['phone'] : '');
            $('#company_site').val(info['website'] != undefined ? info['website'] : '');
            $('#company_address').val(info['address'] != undefined ? info['address'] : '');
        }
    });
</script>

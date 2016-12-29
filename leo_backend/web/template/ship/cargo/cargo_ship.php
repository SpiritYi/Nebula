<style type="text/css">
    .list-container {
        margin-top: 20px;
        padding: 15px;
        border-top: 1px solid #EEE;
    }
    .list-table tbody tr td div {
        height: 60px;
        overflow: hidden;
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
        <input type="button" id="cargo_add_modal_btn" class="btn btn-default" value="添加物品" />
    </div>
</div>

<div class="row list-container">
    <div class="">
        <table class="table table-striped list-table">
            <thead>
                <tr>
                    <th style="width: 50px;">编号</th>
                    <th style="width: 120px;">名称</th>
                    <th style="width: 80px;">价格</th>
                    <th>描述</th>
                    <th>详情网页</th>
                    <th style="width: 100px;">所属公司</th>
                    <th style="width: 100px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->cargoList as $item) { ?>
                    <tr data-id="<?php echo $item['id']; ?>">
                        <td id="cargo_source_<?php echo $item['id']; ?>" style="display: none"><?php echo json_encode($item); ?></td>
                        <td class="cargo-item"><?php echo $item['id']; ?></td>
                        <td class="cargo-item cargo-name"><div><?php echo $item['name']; ?></div></td>
                        <td class="cargo-item cargo-price"><div><?php echo $item['price']; ?></div></td>
                        <td class="cargo-item cargo-content"><div><?php echo str_replace("\n", "<br />", $item['content']); ?></div></td>
                        <td class="cargo-item cargo-website"><div><?php echo $item['desc_website']; ?></div></td>
                        <td class="cargo-item cargo-company"><?php echo $item['company_name']; ?></td>
                        <td>
                            <button type="button" class="btn btn-info btn-xs edit-btn" data-id="<?php echo $item['id']; ?>">编辑</button>
                            <button type="button" class="btn btn-danger btn-xs delete-btn" data-id="<?php echo $item['id']; ?>">删除</button>
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

<!-- 物品信息编辑模态框 -->
<div id="cargo_edit_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">company_info
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 id="edit_title">添加物品</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">物品名称</span>
                                <input type="text" id="cargo_name" class="form-control" placeholder="" />
                                <input type="hidden" id="cargo_id" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">物品价格</span>
                                <input type="text" id="cargo_price" class="form-control" placeholder="" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">详细描述</span>
                                <textarea id="cargo_content" class="form-control" rows="5" title="描述"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">官方网页</span>
                                <input type="text" id="cargo_site" class="form-control" placeholder="" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">所属公司</span>
                                <select id="cargo_company" class="selectpicker form-control" title="选择公司">
                                    <?php foreach ($this->companyList as $cmpItem) { ?>
                                        <option value="<?php echo $cmpItem['cid']; ?>"><?php echo $cmpItem['name']; ?></option>
                                    <?php } ?>
                                    <!-- <option>Abcam</option>
                                    <option>辉瑞制药</option> -->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer submit-group">
                <img src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/loading.gif" style="display:none;" />
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <button type="button" id="edit_submit" class="btn btn-primary">提 交</button>
            </div>
        </div>
    </div>
</div>

<!-- 展示物品资料信息 -->
<div id="cargo_info_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">company_info
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 id="info_title">公司信息</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row info_board" style="margin-bottom: 15px;">
                        <div class="col-lg-12 column">
                            <h4>名称</h4>
                            <div id="info_name"></div>
                            <input type="hidden" id="info_id" />
                        </div>
                        <div class="col-lg-12 column">
                            <h4>价格</h4>
                            <div id="info_price"></div>
                        </div>
                        <div class="col-lg-12 column">
                            <h4>描述</h4>
                            <div id="info_content"></div>
                        </div>
                        <div class="col-lg-12 column">
                            <h4>详情网页</h4>
                            <div id="info_website"></div>
                        </div>
                        <div class="col-lg-12">
                            <h4>所属公司</h4>
                            <div id="info_company"></div>
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
    seajs.use(['NB'], function (NB) {
        NB.navActive($('#nav_cargoship'));

        $('#cargo_add_modal_btn').click(function() {
            var cargoInfo = [];
            cargoInfo.edit_title = '添加物品';
            initEditModal(cargoInfo);

            $('#cargo_edit_modal').modal({backdrop: 'static'});
        });

        //提交数据按钮
        $('#edit_submit').click(function() {
            var cargoId = $('#cargo_id').val(), name = $('#cargo_name').val(), content = $('#cargo_content').val(),
                price = $('#cargo_price').val(), companyId = $('#cargo_company').val(), website = $('#cargo_site').val();
            var method = 'POST', requestUrl = '<?php echo DomainConfig::API_DOMAIN; ?>' + '/leo/backend/cargo/leocargobk/';
            if (cargoId) {      //提供id 是更新操作
                method = 'PUT';
                requestUrl += cargoId +'/';
            }
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: method,     //更新或者新创建
                data: {
                    name: name,
                    price: price,
                    content: content,
                    company_id: companyId,
                    desc_website: website
                },
                url: requestUrl,
                success: function(data) {
                    NB.alert(data.message, 'success');

                    setTimeout("self.location.reload()", 1000);
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });

        //表格栏点击
        $('.cargo-item').hover(
            function() {
                $(this).parent().addClass('hover');
            }, function() {
                $(this).parent().removeClass('hover');
            }
        ).click(function() {
            var id = $(this).parent().data('id'), trSourceSlt = '#cargo_source_' + id;
            var itemArr = JSON.parse($(trSourceSlt).html());
            itemArr.title = '物品信息';

            initInfoModal(itemArr);
            $('#delete_group').hide();

            $('#cargo_info_modal').modal();
        });

        //编辑按钮, 弹出编辑框
        $('.edit-btn').click(function() {
            var id = $(this).data('id'), trSourceSlt = '#cargo_source_' + id;
            var itemArr = JSON.parse($(trSourceSlt).html());
            itemArr.edit_title = '编辑物品信息';

            initEditModal(itemArr);

            $('#cargo_edit_modal').modal({backdrop: 'static'});
        });

        //编辑模态框初始化
        function initEditModal(info) {
            $('#cargo_id').val(info.id != undefined ? info.id : '');
            $('#edit_title').html(info.edit_title != undefined ? info.edit_title : '');
            $('#cargo_name').val(info.name != undefined ? info.name : '');
            $('#cargo_price').val(info.price != undefined ? info.price : '');
            $('#cargo_content').val(info.content != undefined ? info.content : '');
            $('#cargo_site').val(info.desc_website != undefined ? info.desc_website : '');
            if (info.company_id != undefined) {
                $('#cargo_company').find("option[value='" + info.company_id + "']").attr("selected", true).change();
            }
        }

        //点击删除, 弹出删除确认框
        $('.delete-btn').click(function() {
            var id = $(this).data('id'), trSourceSlt = '#cargo_source_' + id;
            var itemArr = JSON.parse($(trSourceSlt).html());
            itemArr.title = '删除物品';

            initInfoModal(itemArr);
            $('#delete_group').show();

            $('#cargo_info_modal').modal();
        });
        //确认删除
        $('#info_delete').click(function() {
            var id = $('#info_id').val();
            NB.apiAjax({
                loading: $('.submit-group img'),
                type: 'DELETE',     //删除
                data: {},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/leo/backend/cargo/leocargobk/' + id + '/',
                success: function(data) {
                    NB.alert(data.message, 'success');

                    setTimeout("self.location.reload()", 1000);
                },
                error: function(data) {
                    NB.alert(data.message, 'danger');
                }
            });
        });

        function initInfoModal(info) {
            $('#info_title').html(info.title);
            $('#info_id').val(info.id);
            $('#info_name').html(info.name != undefined ? info.name : '(空)');
            $('#info_price').html(info.price != undefined ? info.price : '(空)');
            $('#info_content').html(info.content != undefined ? info.content.replace("\n", '<br />') : '(空)');
            $('#info_website').html(info.desc_website != undefined ? info.desc_website : '(空)');
            $('#info_company').html(info.company_name != undefined ? info.company_name : '(空)');
        }
    });
</script>
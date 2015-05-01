//Nebula 组件js

define(function(require, exports) {

    var config = require('script/base/nbconfig');

    //api 专用请求封装函数
    exports.apiAjax = function(args) {
        // config.abc();
        if (!args.data)
            args.data = {};
        args.data[config.USER_VERIFY_COOKIE_KEY] = $.cookie(config.USER_VERIFY_COOKIE_KEY);
        if (args.loading)
            args.loading.show();
        $.ajax({
            type: args.type,
            data: JSON.stringify(args.data),
            url: args.url,
            contentType: 'Application/json',
            dataType: args.dataType || 'json',
            success: function(data) {
                if (args.loading)
                    args.loading.hide();
                args.success(data);
            },
            error: function(data) {
                if (args.loading)
                    args.loading.hide();
                var errorData = {};
                try{
                    errorData = $.parseJSON(data.responseText);
                } catch(e) {
                    errorData['message'] = data.responseText;
                }
                if (args.error)
                    args.error(errorData);
            }
        });
    }

    exports.navActive = function(selecter) {
        $().ready(function() {
            selecter.addClass('active');
        });
    }

    //提示框
    //type 为bootstrap alert- css 后缀， success, info, warning, danger
    exports.alert = function (text, type) {
        type = type || 'info';
        $('.handle-tip').html(text).autoShowAndHide(type);
    }
    //需要手动关闭的提示框
    exports.alertClose = function(text, type) {
        type = type || 'info';
        $('.handle-tip').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + text);
        var left = parseFloat($('.handle-tip').width()) / 2;
        $('.handle-tip').css({'margin-left': '-' + left + 'px', 'display': 'block', 'opacity': 0.85})
            .attr('class', 'handle-tip alert alert-dismissible alert-' + type);
    }
});


//注册NB.tip
!function($) {
    //初始化框
    if ($('.handle-tip').length == 0)
        $('body').append('<div class="handle-tip" style="position:fixed; top: 80%; left:50%; z-index: 999999; opacity: 0.85"></div>');

    $.fn.extend({
        autoShowAndHide: function (type) {
            var left = parseFloat($('.handle-tip').width()) / 2;
            $('.handle-tip').css({ 'opacity': '0', 'margin-left': '-' + left + 'px', 'display': 'block' })
                .attr('class', 'handle-tip alert alert-' + type).stop()
                .animate({ 'opacity': 1 }, 200, function () {
                    $(this).animate({ 'opacity': 0.5 }, 5000, function () {
                        $(this).css('display', 'none');
                    });
                });
        }
    })
}($)

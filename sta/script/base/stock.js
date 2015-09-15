//stock 操作js基础方法

define(function(require, exports) {

    var NB = require('script/base/nb.js');
    var config = require('script/base/nbconfig.js');

    /*
     * 初始化股票联想输入框
     * @param args object
     *          - selector: $('#xxx')       //输入框对象
     *          - updater: function() { }   //选中联想项之后的回调函数
     */
    exports.initStockSelect = function(args) {
        var companyObj;
        args.selector.typeahead({
            source: function(query, process) {
                NB.apiAjax({
                    type: 'GET',
                    data: {"type": "suggestion", "query": query},
                    url: config.API_DOMAIN + '/stock/company/information/',
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
                if (args.updaterBack) { //选择之后的回调函数
                    args.updaterBack();
                }
                return info['sname'];
            }
        });
    };

    /**
     * 高亮价格变化栏
     */
    exports.highlightField = function(selector, colorClass) {
            selector.removeClass('stock-up-bk stock-under-bk field-recover');
            selector.addClass(colorClass + '-bk');
            setTimeout(function() {
                selector.addClass('field-recover');
            }, 500);
        }

    /**
     * 循环获取交易市场状态
     * @param obj
     *          - is_exchange   bool    //是否交易时间
     */
    exports.getMarketStatus = function(obj) {
        function refreshStatus() {
            NB.apiAjax({
                type: 'GET',
                url: config.API_DOMAIN + '/stock/company/market/status/',
                success: function(data) {
                    obj.is_exchange = data.data.is_exchange;
                }
            });
        }
        setInterval(function() { refreshStatus(); }, 60 * 1000);
    }
});

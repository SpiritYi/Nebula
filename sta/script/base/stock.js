//stock 操作js基础方法

define(function(require, exports) {

    var NB = require('script/base/nb.js');
    var config = require('script/base/nbconfig.js');

    exports.initStockSelect = function(args) {
        var companyObj;
        args.selector.typeahead({
            source: function(query, process) {
                console.log(query);
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
});

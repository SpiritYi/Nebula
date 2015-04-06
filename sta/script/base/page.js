//网站js config

define(function(require, exports) {

    var $ = require('jquery');

    exports.initNavBar = function(navli) {
        $().ready(function() {
            navli.addClass('active');
        });
    }
});

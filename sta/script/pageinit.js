//网站js config

define(function(require, exports) {

    var $ = require('jquery');

    exports.initNavBar = function(navli) {
        // $('ul.navbar li').each(function() {
            // $(this).removeClass('active');
        // });
        navli.addClass('active');
    }
});

<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
        <?php $this->staExport('/css/Base.css'); ?>
        <!-- 新 Bootstrap 核心 CSS 文件 -->
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <?php $this->staExport('/css/lib/bootstrap-select.min.css'); ?>

        <?php $this->staExport('/css/master.css'); ?>
        <?php $this->loadHead(); ?>
        <title>Nebula Stock</title>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">Hello</a>
                </div>
                <div id="nav" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a id="navbar_signout">Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php $this->staExport('script/lib/sea.js'); ?>
        <script type="text/javascript">
            seajs.config({
                'base': '<?php echo DomainConfig::STA_DOMAIN; ?>',
                'alias': {
                    'NB': 'script/base/nb.js'
                }
            });
        </script>
        <?php $this->staExport('/script/lib/jquery-2.1.3.js'); ?>
        <?php $this->staExport('/script/lib/jquery.cookie-1.4.1.min.js'); ?>

        <!-- bootstrap select http://silviomoreto.github.io/bootstrap-select/ -->
        <?php $this->staExport('/script/lib/bootstrap-select.min.js'); ?>

        <?php $this->action(); ?>
        <footer class="footer">
            <div class="container">
                <p class="copyright" data-ceo="SpiritYi">© <?php echo date('Y'); ?> Nebula Investment Fund. All Rights Reserved.</p>
            </div>
        </footer>

        <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

        <script type="text/javascript">
            $('#navbar_signout').click(function() {
                var flag = $.removeCookie('verify_user', {path: '/'});
                location.reload();
            });
        </script>
    <body>
</html>

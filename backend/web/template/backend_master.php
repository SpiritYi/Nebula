<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="Shortcut Icon" href="<?php echo DomainConfig::STA_DOMAIN; ?>/image/logo/nebula_logo_simple_favicon.png" />
        <?php $this->staExport('/css/Base.css'); ?>
        <!-- 新 Bootstrap 核心 CSS 文件 -->
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <?php $this->staExport('/css/lib/bootstrap-select.min.css'); ?>

        <?php $this->staExport('/css/backend/backend_master.css'); ?>
        <?php $this->loadHead(); ?>
        <title>星云舰船</title>
        <style type="text/css">
            .navbar-brand {
                padding: 4px;
                margin-right: 15px;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top" style="padding: 0px 15px 0px 3px;">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">
                        <img alt="Nebula" height="42px" src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/logo/nebula_logo_64.png" >
                    </a>
                </div>
                <div id="nav" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a id="navbar_signout">Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2 sidebar">
                    <ul class="nav nav-sidebar">
                        <li id="nav_usership"><a href="/ship/user/usership">用户管理</a></li>
                        <li id="nav_companyship"><a href="/ship/company/companyship">公司数据</a></li>
                        <li id="nav_exchange"><a href="/ship/stock/stockship">股票交易</a></li>
                    </ul>
                    <footer class="footer">
                        <p class="copyright" data-ceo="SpiritYi">© <?php echo date('Y'); ?> Nebula Fund.</p>
                    </footer>
                </div>
                <div class="col-lg-10 col-lg-offset-2 main-wrap">
                    <?php $this->staExport('script/lib/sea.js'); ?>
                    <script type="text/javascript">
                        seajs.config({
                            'base': '<?php echo DomainConfig::STA_DOMAIN; ?>',
                            'alias': {
                                'NB': 'script/base/nb.js',
                                'Stock': 'script/base/stock.js'
                            }
                        });
                    </script>
                    <?php $this->staExport('/script/lib/jquery-2.1.3.js'); ?>
                    <?php $this->staExport('/script/lib/jquery.cookie-1.4.1.min.js'); ?>

                    <!-- bootstrap select http://silviomoreto.github.io/bootstrap-select/ -->
                    <?php $this->staExport('/script/lib/bootstrap-select.min.js'); ?>
                    <!-- https://github.com/bassjobsen/Bootstrap-3-Typeahead -->
                    <?php $this->staExport('/script/lib/bootstrap3-typeahead.min.js'); ?>

                    <?php $this->action(); ?>
                </div>
            </div>
        </div>

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

<!DOCTYPE HTML>
<html>
    <head>
        <?php $this->staExport('/css/Base.css'); ?>
        <!-- 新 Bootstrap 核心 CSS 文件 -->
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">

        <!-- 可选的Bootstrap主题文件（一般不用引入） -->
        <!-- <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap-theme.min.css"> -->
        <?php $this->loadHead(); ?>
        <title>Nebula Website</title>
        <style type="text/css">
            .footer {
                margin-top: 20px;
                background-color: #F5F5F5;
            }
            .footer .copyright {
                margin:20px 0px;
                color: #E1E1E1;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">
                        <img alt="Nebula" height="24px" src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/logo/nebula_logo_24.png" >
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li id="navbar_default"><a href="/">首页<span class="sr-only">(current)</span></a></li>
                        <!-- <li><a href="/">知识堂</a></li> -->
                        <li id="navbar_notice"><a href="/article/noticelist">公告板</a></li>
                        <li id="navbar_earnings"><a href="/company/earnings">投资收益</a></li>
                        <li id="navbar_about"><a href="/company/about">关于公司</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php $this->staExport('script/sea.js'); ?>
        <script type="text/javascript">
            seajs.config({
                'base': '<?php echo DomainConfig::STA_DOMAIN; ?>',
                'alias': {
                    'jquery': 'script/jquery-2.1.3.js'
                }
            });
        </script>
        <script src="<?php echo DomainConfig::STA_DOMAIN; ?>/script/jquery-2.1.3.js"></script>
        <script src="<?php echo DomainConfig::STA_DOMAIN; ?>/script/highcharts.js"></script>

        <?php $this->action(); ?>
        <footer class="footer">
            <div class="container">
                <p class="copyright" data-ceo="SpiritYi">© <?php echo date('Y'); ?> Nebula Investment Fund. All Rights Reserved.</p>
            </div>
        </footer>
        <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
        <!-- // <script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script> -->


        <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

    </body>
</html>

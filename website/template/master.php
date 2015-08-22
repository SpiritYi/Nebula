<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="Shortcut Icon" href="<?php echo DomainConfig::STA_DOMAIN; ?>/image/logo/nebula_logo_simple_favicon.png" />
        <?php $this->staExport('/css/Base.css'); ?>
        <!-- 新 Bootstrap 核心 CSS 文件 -->
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <?php $this->staExport('/css/master.css'); ?>

        <!-- 可选的Bootstrap主题文件（一般不用引入） -->
        <!-- <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap-theme.min.css"> -->
        <?php $this->loadHead(); ?>
        <title>Nebula Website</title>
        <style type="text/css">
            .navbar-brand {
                padding: 4px;
                margin-right: 15px;
            }
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
                        <img alt="Nebula" height="42px" src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/logo/nebula_logo_64.png" >
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li id="navbar_default"><a href="/">首页<span class="sr-only">(current)</span></a></li>
                        <!-- <li><a href="/">知识堂</a></li> -->
                        <li id="navbar_notice"><a href="/article/noticelist">公告板</a></li>
                        <li id="navbar_earnings"><a href="/company/earnings">投资收益</a></li>
                        <li id="navbar_about" class="dropdown">
                            <a href="/company/about" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">关于公司 <span class="caret"></span></a>
                            <ul class="dropdown-menu nav-dropdown" role="menu">
                                <li><a href="/company/about">公司简介</a></li>
                                <li><a href="/company/siteupdate">网站更新</a></li>
                                <li><a href="/company/support">服务信箱</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li id="navbar_user"><a href="/user/property"><?php echo $this->userInfo['nickname']; ?></a></li>
                        <li id="navbar_signout"><a>登出</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php $this->staExport('script/lib/sea.js'); ?>
        <script type="text/javascript">
            seajs.config({
                'base': '<?php echo DomainConfig::STA_DOMAIN; ?>',
                'alias': {
                    // 'jquery': 'script/jquery-2.1.3.js',
                    'NB': 'script/base/nb.js'
                }
            });
        </script>
        <?php $this->staExport('/script/lib/jquery-2.1.3.js'); ?>
        <?php $this->staExport('/script/lib/jquery.cookie-1.4.1.min.js'); ?>
        <?php $this->staExport('/script/lib/highcharts.js'); ?>

        <?php $this->action(); ?>
        <footer class="footer">
            <div class="container">
                <p class="copyright" data-ceo="SpiritYi">© <?php echo date('Y'); ?> Nebula Investment Fund. All Rights Reserved.</p>
            </div>
        </footer>

        <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

        <script type="text/javascript">
            seajs.use([], function() {
                $('#navbar_signout').click(function() {
                    var flag = $.removeCookie('verify_user', {path: '/'});
                    location.reload();
                });
            });
        </script>
        <!-- 统计代码 -->
        <div style="display: none;">
            <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1254926362'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/stat.php%3Fid%3D1254926362' type='text/javascript'%3E%3C/script%3E"));</script>
        </div>
    </body>
</html>

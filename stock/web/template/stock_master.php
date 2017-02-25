<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="Shortcut Icon" href="<?php echo DomainConfig::STA_DOMAIN; ?>/image/logo/nebula_logo_simple_favicon.png" />
        <?php $this->staExport('/css/Base.css'); ?>
        <?php $this->staExport('/css/stock/global.css'); ?>
        <!-- 新 Bootstrap 核心 CSS 文件 -->
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <?php $this->staExport('/css/lib/bootstrap-select.min.css'); ?>

        <?php $this->staExport('/css/master.css'); ?>
        <?php $this->loadHead(); ?>
        <title>Nebula Stock</title>
        <style type="text/css">
            body, .container {
                min-width: 1200px;
            }
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
                        <img alt="Nebula" height="42px" src="<?php echo DomainConfig::STA_DOMAIN; ?>/image/logo/nebula_logo_stock_128.png" >
                    </a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li id="navbar_holdings"><a href="/stock/holdings">我的持仓</a></li>
                        <li id="navbar_losslimit"><a href="/exchange/losslimit">设置止损</a></li>
                        <li id="navbar_delegate"><a href="/exchange/delegate">当日委托</a></li>
                        <li id="navbar_record"><a href="/stock/exchange">交易记录</a></li>
                        <li id="navbar_account" class="dropdown">
                            <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">账号 <span class="caret"></span></a>
                            <ul class="dropdown-menu nav-dropdown" role="menu">
                                <li><a href="/user/message">消息通知</a></li>
                                <li><a href="/account/password">修改密码</a></li>
                            </ul>
                        </li>
                        <li id="navbar_analysis"><a href="/user/analysis">数据分析</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li id="navbar_user"><a href="/stock/holdings"><?php echo $this->userInfo['nickname']; ?></a></li>
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
                    'NB': 'script/base/nb.js',
                    'Stock': 'script/base/stock.js',
                }
            });
        </script>
        <?php $this->staExport('/script/lib/jquery-2.1.3.js'); ?>
        <?php $this->staExport('/script/lib/jquery.cookie-1.4.1.min.js'); ?>
        <?php $this->staExport('/script/lib/highcharts.js'); ?>

        <!-- bootstrap select http://silviomoreto.github.io/bootstrap-select/ -->
        <?php $this->staExport('/script/lib/bootstrap-select.min.js'); ?>
        <!-- https://github.com/bassjobsen/Bootstrap-3-Typeahead -->
        <?php $this->staExport('/script/lib/bootstrap3-typeahead.min.js'); ?>

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
                var flag = $.removeCookie('<?php echo StockUserNamespace::USER_VERIFY_COOKIE_KEY; ?>', {path: '/'});
                location.reload();
            });
        </script>
    <body>
</html>

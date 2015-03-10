<!DOCTYPE HTML>
<html>
    <head>
        <!-- <?php echo $this->staticFileLink('JS', '/sea.js'); ?> -->
        <!-- 新 Bootstrap 核心 CSS 文件 -->
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">

        <!-- 可选的Bootstrap主题文件（一般不用引入） -->
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

        <?php $this->loadHead(); ?>
        <title>Nebula Website</title>
        <style type="text/css">
            .footer {
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
                        <li class="active"><a href="/">首页<span class="sr-only">(current)</span></a></li>
                        <li><a href="/">知识堂</a></li>
                        <li><a href="#">关于公司</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php $this->action(); ?>
        <footer class="footer">
            <div class="container">
                <p class="copyright" data-ceo="SpiritYi">© <?php echo date('Y'); ?> Nebula. All Rights Reserved.</p>
            </div>
        </footer>
        <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
        <script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>

        <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        seajs.use('Common/Init', function(Init) {
            //执行页面初始化
            // Init.run();
        });
    </script>
    </body>
</html>

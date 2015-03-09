<!DOCTYPE HTML>
<html>
    <head>
        <?php echo $this->staticFileLink('CSS', '/css/Base.css'); ?>
        <?php echo $this->staticFileLink('CSS', '/css/Master.css'); ?>
        <?php echo $this->staticFileLink('JS', '/sea.js'); ?>
        <?php $this->loadHead(); ?>
        <title>Nebula Website</title>
    </head>
    <body>
        <div id="header">
            <div id="logo">
                NEBULA
            </div>
            <div id="nav">
                <ul>
                    <li>
                        <a href="/Index">Home</a>
                    </li>
                    <li>
                        <a href="/CompanyLine">Comapny Line</a>
                    </li>
                    <li>
                        <a href="/User/UserInfo"> User</a>
                    </li>
                </ul>
                <ul id="loginUser">

                </ul>
            </div>
        </div>
        <div id="wrap">
            <div>
                <?php $this->loadContent(); ?>
            </div>
        </div>
        <div id="footer">
            <div>
                <p>© <?php echo date('Y'); ?> Nebula</p>
            </div>
            <div id="console">
            </div>
        </div>
    </body>
    <script type="text/javascript">
        seajs.use('Common/Init', function(Init) {
            //执行页面初始化
            Init.run();
        });
    </script>
</html>

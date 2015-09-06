<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <link rel="Shortcut Icon" href="<?php echo DomainConfig::STA_DOMAIN; ?>/image/logo/nebula_logo_simple_favicon.png" />
        <?php $this->staExport('/css/Base.css'); ?>
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">

        <?php $this->loadHead(); ?>
        <title>Nebula Stock</title>
    </head>
    <body>
        <?php $this->staExport('script/lib/sea.js'); ?>
        <script type="text/javascript">
            seajs.config({
                'base': '<?php echo DomainConfig::STA_DOMAIN; ?>',
                'alias': {
                    'jquery': 'script/jquery-2.1.3.js',
                    'NB': 'script/base/nb.js'
                    // 'moment': 'script/lib/moment.min.js'
                }
            });
        </script>
        <?php $this->staExport('/script/lib/jquery-2.1.3.js'); ?>
        <?php $this->staExport('/script/lib/jquery.cookie-1.4.1.min.js'); ?>
        <?php $this->staExport('/script/lib/moment.min.js'); ?>
        <?php $this->staExport('/script/base/nb.js'); ?>

        <?php $this->action(); ?>

        <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    </body>
</html>

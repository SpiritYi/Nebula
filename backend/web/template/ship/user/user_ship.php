<div>
    <input type="button" id="property_modal_btn" class="btn btn-default" value="资产配置" />
    <?php $this->render('/ship/user/property_bk.php'); ?>
</div>

<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#nav_usership'));

        $('#property_modal_btn').click(function() {
            $('#property_modal').modal({backdrop: 'static'});
        });
    });
</script>

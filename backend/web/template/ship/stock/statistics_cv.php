<style type="text/css">
    .charts_board {
        padding: 10px 25px;
        background-color: #F5F5F5;
        border-top: 1px solid #EEE;
        border-bottom: 1px solid #EEE;
    }
    .charts_board_bottom {
        margin-bottom: 35px;
    }
</style>

<?php foreach ($this->statisticsBlock as $i => $blockItem) { ?>
    <div class="row charts_board <?php if (isset($this->statisticsBlock[$i + 1])) { echo 'charts_board_bottom'; } ?>">
        <div class="statistics_line" data-category="<?php echo $blockItem['category']; ?>" data-title="<?php echo $blockItem['title']; ?>"></div>
    </div>
<?php } ?>

<?php $this->staExport('/script/lib/highcharts.js'); ?>
<script type="text/javascript">
    seajs.use(['NB'], function(NB) {
        NB.navActive($('#nav_statistics'));

        $('.statistics_line').each(function() {
            initCharts($(this));
        });

        function initCharts($divObj) {
            NB.apiAjax({
                type: 'GET',
                data: {'category': $divObj.data('category')},
                url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/backend/stock/statisticsbk/',
                dataType: 'json',
                success: function(data) {
                    $divObj.highcharts({
                        chart: {
                            type: 'line'
                        },
                        title: {
                            text: $divObj.data('title')
                        },
                        xAxis: {
                            categories: data.data.date_list
                        },
                        yAxis: {
                            title: {
                                text: '家数'
                            }
                        },
                        tooltip: {
                            valueSuffix: ' 个'
                        },
                        colors: data.data.color_list,
                        plotOptions: {
                            line: {
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: data.data.charts_list
                    });
                }
            });
        }
    })
</script>
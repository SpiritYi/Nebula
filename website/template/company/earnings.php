<div class="container">
    <div id="earnings_charts">
    </div>
    <p>说明：每月结算时间为当月10日开盘，到次月9日收盘，如遇假期，以最近有效数据为准。</p>
</div>

<script text="text/javascript">
    seajs.use(['jquery', 'script/pageinit'], function($, pageinit) {
        pageinit.initNavBar($('#navbar_earnings'));

        $.ajax({
            type: 'GET',
            url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/company/earnings/',
            dataType: 'json',
            success: function(data) {
                console.log(data.data.charts_list);
                $('#earnings_charts').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '投资收益对比上证A股指数'
                    },
                    subtitle: {
                        text: '按月结算，复合累进'
                    },
                    xAxis: {
                        // categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
                        categories: data.data.date_list
                    },
                    yAxis: {
                        title: {
                            text: '营收百分比'
                        }
                    },
                    colors: ['#A020F0', '#000'],
                    tooltip: {
                        valueSuffix: '%'
                    },
                    plotOptions: {
                        column: {
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    if (this.y < 0) {
                                        return '<span style="color:#00FF00">' + this.y + '%</span>';
                                    } else {
                                        return '<span style="color:red;">' + this.y + '%</span>';
                                    }
                                }
                            },
                            // enableMouseTracking: false
                        }
                    },
                    credits: {
                        enabled: true
                    },
                    series: data.data.charts_list
                });
            }
        });
    });
</script>

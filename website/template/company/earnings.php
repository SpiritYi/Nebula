<div class="container">
    <div id="earnings_charts">
    </div>
    <div id="earnings_line" style="margin-top: 20px;">
    </div>
    <p class="note">说明：每月结算时间为当月10日开盘，到次月9日收盘，如遇假期，以最近有效数据为准。</p>
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
                        }
                    },
                    credits: {
                        enabled: true
                    },
                    series: data.data.charts_list
                });
            }
        });

        $.ajax({
            type: 'GET',
            url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/company/earnings/line/',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $('#earnings_line').highcharts({
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: '资产累计走势曲线'
                    },
                    subtitle: {
                        text: '按月结算，累进入投'
                    },
                    xAxis: {
                        categories: data.data.date_list
                    },
                    yAxis: {
                        title: {
                            text: '所持份额(份)'
                        }
                    },
                    tooltip: {
                        valueSuffix: ' 份'
                    },
                    colors: ['#A020F0'],
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            // enableMouseTracking: false
                        }
                    },
                    series: data.data.charts_list
                    // series: [{
                    //     name: 'Tokyo',
                    //     data: [7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
                    // }, {
                    //     name: 'London',
                    //     data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
                    // }]
                });
            }
        })
    });
</script>

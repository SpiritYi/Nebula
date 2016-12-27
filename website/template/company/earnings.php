<style type="text/css">
    .year-link {
        margin-top: 25px;
        margin-bottom: 20px;
    }
    .year-link div a {
        float: left;
        width: 100%;
        padding: 20px 50px;
        text-align: center;
        font-size: 150%;
        background-color: #F5F5F5;
    }
</style>
<div class="container">
    <input type="hidden" id="show_year" value="<?php echo !empty($_GET['y']) ? $_GET['y'] : date('Y'); ?>">
    <div class="row">
        <div id="earnings_charts" style="margin-top:15px;margin-bottom:50px;">
        </div>
        <div id="earnings_line" style="margin-bottom:15px;">
        </div>
    </div>
    <p class="note">说明：每月结算时间周期为当月10日开盘，到次月9日收盘，如遇假期，以最近有效数据为准。</p>
</div>
<div class="container">
    <div class="row year-link">
        <div class="col-lg-4">
            <a href="/company/earnings?y=2016">2016投资收益</a>
        </div>
        <div class="col-lg-4">
            <a href="/company/earnings?y=2015">2015投资收益</a>
        </div>
        <div class="col-lg-4">
            <a href="/company/earnings?y=2014">2014投资收益</a>
        </div>
    </div>
</div>

<script text="text/javascript">
    seajs.use(['NB', 'script/base/page'], function(NB, page) {
        NB.navActive($('#navbar_earnings'));

        var year = $('#show_year').val();

        NB.apiAjax({
            type: 'GET',
            data: {'y': year},
            url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/company/earnings/',
            dataType: 'json',
            success: function(data) {
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
                            text: '营收百分比 (%)'
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
                                        return '<span style="color:#00FF00">' + this.y + '</span>';
                                    } else {
                                        return '<span style="color:red;">' + this.y + '</span>';
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

        NB.apiAjax({
            type: 'GET',
            data: {'y': year},
            url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/v1/company/earnings/line/',
            dataType: 'json',
            success: function(data) {
                $('#earnings_line').highcharts({
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: '投资收益走势曲线'
                    },
                    subtitle: {
                        text: '按月结算，累进入投'
                    },
                    xAxis: {
                        categories: data.data.date_list
                    },
                    yAxis: {
                        title: {
                            text: '所持份额 (份)'
                        }
                    },
                    tooltip: {
                        valueSuffix: ' 份'
                    },
                    colors: ['#A020F0', '#000'],
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
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

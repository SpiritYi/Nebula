<div class="container">
    <div id="property_line"></div>
</div>

<script type="application/javascript">
    seajs.use(["NB"], function(NB) {
        NB.navActive($('#navbar_analysis'));

        NB.apiAjax({
            type: 'GET',
            data: {'y': 2017},
            url: '<?php echo DomainConfig::API_DOMAIN; ?>' + '/stock/user/property/',
            dataType: 'json',
            success: function(data) {
                $('#property_line').highcharts({
                    chart: {
                        zoomType: 'xy'
                    },
                    title: {
                        text: '总资产对比上证指数'
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: [{
                        categories: data.data.date_list,
                        crosshair: true
                    }],
                    yAxis: [{
                        title:{
                            text: '万元',
                            style:{
                                color: '#A020F0'
                            }
                        },
                        labels:{
                            formatter: function() {
                                return (this.value / 10000).toFixed(2);
                            },
                            style:{
                                color: '#A020F0'
                            }

                        }
                    }, {
                        title:{
                            text: ""
                        },
                        labels:{
                            style:{
                                color: '#000'
                            }
                        },
                        opposite: true
                    }],
                    tooltip: {
                        shared: true
                    },
                    colors: ['#A020F0', '#000'],
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
define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts'], function ($, undefined, Backend, Table, Form, ECharts) {

    var Controller = {
        index: function () {
            $(document).on('change', 'select[name="site_id"]', function (event) {
                var value = $(event.currentTarget).val();
                var href = location.href;
                if (href.indexOf('?') !== -1) {
                    href = href + '&site_id=' + value;
                } else {
                    href = href + '?site_id=' + value;
                }
                location.href = href;
            });
            $(document).on('change', 'select[name="date"]', function (event) {
                var value = $(event.currentTarget).val();
                var href = location.href;
                if (href.indexOf('?') !== -1) {
                    href = href + '&date=' + value;
                } else {
                    href = href + '?date=' + value;
                }
                location.href = href;
            });
            // 绘制图标
            var myChart = ECharts.init(document.getElementById('containers'), null, {
                renderer: 'canvas',
                useDirtyRect: false
            });
            var option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {
                    show: true,
                    bottom: 0,
                    left: '5%'
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        data: Config.categoryList
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: __('Pv'),
                        type: 'bar',
                        stack: 'Ad',
                        emphasis: {
                            focus: 'series'
                        },
                        itemStyle: {
                            color: '#91cc75'
                        },
                        data: Config.pvArray
                    },
                    {
                        name: __('Uv'),
                        type: 'bar',
                        stack: 'Ad',
                        emphasis: {
                            focus: 'series'
                        },
                        itemStyle: {
                            color: '#fac858'
                        },
                        data: Config.uvArray
                    },
                    {
                        name: __('Rv'),
                        type: 'bar',
                        stack: 'Ad',
                        emphasis: {
                            focus: 'series'
                        },
                        itemStyle: {
                            color: '#73c0de'
                        },
                        data: Config.rvArray
                    },
                    {
                        name: __('Vv'),
                        type: 'line',
                        emphasis: {
                            focus: 'series'
                        },
                        itemStyle: {
                            color: '#fc8452'
                        },
                        data: Config.vvArray
                    }
                ]
            };
            $(window).resize(function () {
                myChart.resize();
            });
            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
            window.addEventListener('resize', myChart.resize);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});

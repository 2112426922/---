define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template', 'echarts', 'echarts-theme'], function ($, undefined, Backend, Table, Form, Template, Echarts) {

    var Controller = {
        index: function () {
            // 巡检次数
            var inspectionLine = Echarts.init(document.getElementById('line-inspection'), 'walden');
            var inspectionOption = {
                xAxis: {
                    type: 'category',
                    splitLine: {
                        show: false
                    },
                    data: Config.statisticChart.weekDate
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: Config.statisticChart.inspectionWeekData,
                    type: 'line'
                }]
            };
            inspectionLine.setOption(inspectionOption);

            // 保养次数
            var maintenanceLine = Echarts.init(document.getElementById('line-maintenance'), 'walden');
            var maintenanceOption = {
                xAxis: {
                    type: 'category',
                    splitLine: {
                        show: false
                    },
                    data: Config.statisticChart.weekDate
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: Config.statisticChart.maintenanceWeekData,
                    type: 'line'
                }]
            };
            maintenanceLine.setOption(maintenanceOption);

            // 维修工单数
            var repairLine = Echarts.init(document.getElementById('line-repair'), 'walden');
            var repairOption = {
                xAxis: {
                    type: 'category',
                    splitLine: {
                        show: false
                    },
                    data: Config.statisticChart.weekDate
                },
                yAxis: {
                    type: 'value',
                },
                series: [{
                    data: Config.statisticChart.repairWeekData,
                    type: 'bar',
                }]
            };
            repairLine.setOption(repairOption);

            var pieChart = Echarts.init(document.getElementById('pie-chart'), 'walden');
            var option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b}: {c} ({d}%)'
                },
                legend: {
                    orient: 'vertical',
                    left: 10,
                    data: Config.statisticChart.failureCauseNames
                },
                series: [
                    {
                        name: '故障原因',
                        type: 'pie',
                        radius: ['50%', '70%'],
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: false,
                                position: 'center'
                            },
                            emphasis: {
                                show: true,
                                textStyle: {
                                    fontSize: '30',
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data: Config.statisticChart.failureCause
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            pieChart.setOption(option);
        }
    };

    return Controller;
});

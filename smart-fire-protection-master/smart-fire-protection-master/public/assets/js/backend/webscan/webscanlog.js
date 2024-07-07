define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts'], function ($, undefined, Backend, Table, Form,Echarts) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    "index_url": "webscan/webscanlog/index",
                    "add_url": "",
                    "edit_url": "",
                    "del_url": "",
                    "multi_url": "",
                }
            });
            var table = $("#table");
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'request_url', title:'请求url', operate: 'LIKE'},
                        {field: 'ip', title: __('IP'), operate: 'LIKE',events: Controller.api.events.ip, formatter: Controller.api.formatter.ip},
                        {field: 'method', title: __('请求类型')},
                        {field: 'rkey', title: __('参数')},
                        {field: 'rdata', title: __('攻击值'), operate: 'LIKE'},
                        {field: 'user_agent', title: __('user_agent'), operate: 'LIKE'},
                        {field: 'create_time', title: __('攻击时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'operate', title: __('Operate'), table: table,buttons: [
                                {
                                    name: 'cancel',
                                    text: '加入黑名单',
                                    title: '加入黑名单',
                                    icon: 'fa fa-exclamation-triangle',
                                    classname: 'btn btn-xs btn-danger btn-ajax',
                                    confirm: '确认加入黑名单？',
                                    url: function(row){
                                        return 'webscan/webscanlog/black/ip/'+row.ip;
                                    },
                                    success: function (data, ret) {

                                    }
                                }], formatter: Table.api.formatter.operate}
                    ]
                ],
            });

            // 为表格绑定事件
            Table.api.bindevent(table);//当内容渲染完成后


        },
        dashboard: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(setchart(data));
            function setchart(data){
                // 指定图表的配置项和数据
                var option = {
                    title: {text: '',subtext: ''},
                    tooltip: {trigger: 'axis'},
                    legend: {data: ['攻击次数']},
                    toolbox: {feature: {magicType: {show: true, type: ['stack', 'tiled']},saveAsImage: {show: true}}},
                    xAxis: {type: 'category',boundaryGap: false,data: data.column},
                    yAxis: [{type : 'value'}],
                    grid: [{left: '3%',right: '4%',bottom: '3%',containLabel: true}],
                    series: [{name: '攻击次数',type: 'line',smooth: true,areaStyle: {normal: {}},lineStyle: { normal: {width: 1.5}},data: data.totallist}]
                };
                return option;
            }



            //users_statistics
            $(document).on('click', ".statistics", function () {
                Backend.api.open($(this).attr("href"),$(this).data("title"),[],'80%','80%');
                return false;
            });

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
            },formatter: {//渲染的方法
                ip: function (value, row, index) {
                    return '<a class="btn btn-xs btn-ip bg-success"><i class="fa fa-map-marker"></i> ' + value + '</a>';
                },

            },
            events: {//绑定事件的方法
                ip: {
                    //格式为：方法名+空格+DOM元素
                    'click .btn-ip': function (e, value, row, index) {
                        e.stopPropagation();
                        var container = $("#table").data("bootstrap.table").$container;
                        var options = $("#table").bootstrapTable('getOptions');
                        //这里我们手动将数据填充到表单然后提交
                        $("form.form-commonsearch [name='ip']", container).val(value);
                        $("form.form-commonsearch", container).trigger('submit');
                    }
                },
            }

        }
    };
    return Controller;
});
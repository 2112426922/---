define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts'], function ($, undefined, Backend, Table, Form,Echarts) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    "index_url": "webscan/verifies/index",
                    "add_url": "",
                    "edit_url": "",
                    "del_url": "",
                    "multi_url": "",
                }
            });
            var table = $("#table");
            var method={'local':'本地','official':'官方'};
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pagination: false,
                commonSearch: false,
                search: false,

                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('序号'),formatter:function (value,row,index) {
                                row.id=index;
                                return index;
                        }},
                        {field: 'method', title: __('校验类型'),formatter:function (value,row,index) {
                            return method[value];
                            }},
                        {field: 'md5', title: __('MD5')},
                        {field: 'filename', title: __('文件路径')},
                        {field: 'mktime', title: __('最后更新时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'operate', title: __('Operate'), table: table,buttons: [
                                {
                                    name: 'cancel',
                                    text: '加入信任',
                                    title: '加入信任',
                                    icon: 'fa fa-exclamation-triangle',
                                    classname: 'btn btn-xs btn-danger btn-ajax',
                                    confirm: '加入信任？',
                                    url: function(row,obj){
                                        return 'webscan/verifies/trust/index/'+row.id;
                                    },
                                    success: function (data, ret) {
                                        table.bootstrapTable('refresh');
                                    }
                                },{
                                    name: 'edit1',
                                    text: '查看',
                                    title:"查看",
                                    icon: 'fa fa-eye',
                                    classname: 'btn btn-xs btn-success btn-dialog ',
                                    url: function (row) {
                                        return 'webscan/verifies/show/?filename='+row.filename;
                                    }
                                },], formatter: Table.api.formatter.operate}
                    ]
                ],
            });

            // 为表格绑定事件
            Table.api.bindevent(table);//当内容渲染完成后


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
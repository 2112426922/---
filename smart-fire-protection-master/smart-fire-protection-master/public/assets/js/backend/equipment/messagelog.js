define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'equipment/messagelog/index' + location.search,
                    add_url: 'equipment/messagelog/add',
                    edit_url: 'equipment/messagelog/edit',
                    del_url: 'equipment/messagelog/del',
                    multi_url: 'equipment/messagelog/multi',
                    import_url: 'equipment/messagelog/import',
                    table: 'equipment_messagelog',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false,visible:false},
                        {field: 'code', title: __('Code'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'archive.model', title: __('Model'), operate: false},
                        {field: 'archive.name', title: __('Name'), operate: false},
                        {field: 'archive.region', title: __('Region'), operate: false},
                        {field: 'archive.load_responsible_user.nickname', title: __('Responsible_uid'), formatter: function(value, row){return value + '，' + row.archive.load_responsible_user.mobile}, operate: false},
                        {field: 'temp', title: __('Temp'),
                        formatter: function (value, row, index) {
                            return "<span class='text-danger'>" + value + " ℃</span>";
                        },operate:false
                        },
                        {field: 'hum', title: __('Hum'),
                        formatter: function (value, row, index) {
                            return "<span class='text-danger'>" + value + " %RH</span>";
                        },operate:false
                        },
                        {field: 'press', title: __('Press'),
                        formatter: function (value, row, index) {
                            return "<span class='text-danger'>" + value + " Kmp</span>";
                        }, operate: false},
                        {field: 'light', title: __('Light'),
                        formatter: function (value, row, index) {
                            return "<span class='text-danger'>" + value + " lx</span>";
                        }, operate: false},
                        {field: 'sta', title: __('Sta'),
                        formatter: function (value, row, index) {
                            if(value==0)
                            return "<span class='text-danger'>烟雾正常</span>";
                        else
                        return "<span class='text-danger'>附有可燃性气体</span>";
                        }, operate: false},
                        {field: 'status', title: __('Status'),
                        formatter: function (value, row, index) {
                            if(value==0)
                            return "<span class='text-danger'>设备未运行</span>";
                        else
                        return "<span class='text-danger'>设备已运行</span>";
                        }, operate: false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'record', title: __('Record'), table: table, buttons: [
                            {
                                name: 'messagelog',
                                title: __('Massagelog List'),
                                icon: 'fa fa-list-alt',
                                extend: 'data-area=\'["100%", "100%"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/messagelog/index?code={code}'
                            }
                        ], formatter: Table.api.formatter.buttons, operate: false}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'equipment/messagelog/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'code', title: __('Code'), operate: false},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'equipment/messagelog/restore',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ],
                search: false,
                showToggle: false,
                showColumns: false,
                showExport: false,
                commonSearch: false
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});

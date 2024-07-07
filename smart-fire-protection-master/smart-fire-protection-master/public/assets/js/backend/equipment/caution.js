define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'equipment/caution/index' + location.search,
                    add_url: 'equipment/caution/add',
                    edit_url: 'equipment/caution/edit',
                    del_url: 'equipment/caution/del',
                    multi_url: 'equipment/caution/multi',
                    import_url: 'equipment/caution/import',
                    table: 'equipment_caution',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false,visible:false},
                        {field: 'code', title: __('Code'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'archive.model', title: __('Model'), operate: false},
                        {field: 'archive.name', title: __('Name'), operate: false},
                        {field: 'archive.region', title: __('Region'), operate: false},
                        {field: 'archive.load_responsible_user.nickname', title: __('Responsible_uid'), formatter: function(value, row){return value + '，' + row.archive.load_responsible_user.mobile}, operate: false},
                        {field: 'warning', title: __('Warning'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'equipment.work_status', title: __('Equipment.work_status'), operate: false, searchList: {"repairing": __('Repairing'), "scrapped": __('Scrapped'), "normal": __('Normal'), "sickness": __('Sickness')},custom: {'停机待修': 'info', '正常': 'success', '停机报废': 'danger'}, formatter:Table.api.formatter.label},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});

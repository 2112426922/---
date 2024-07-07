define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'equipment/plan/index' + location.search,
                    add_url: 'equipment/plan/add',
                    edit_url: 'equipment/plan/edit',
                    del_url: 'equipment/plan/del',
                    multi_url: 'equipment/plan/multi',
                    import_url: 'equipment/plan/import',
                    table: 'equipment_plan',
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
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'first_duetime', title: __('First_duetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'last_duetime', title: __('Last_duetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'periodicity', title: __('Periodicity')},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search: false
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'equipment/plan/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
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
                                    url: 'equipment/plan/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'equipment/plan/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        list: function () {
            // 初始化表格参数配置
            Table.api.init();

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'equipment/plan/list' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {field: 'equipment_code', title: __('EquipmentCode')},
                        {field: 'coding', title: __('QrcodeTag'), table: table, buttons: [
                                {
                                    name: 'qrcode',
                                    text: '',
                                    title: __('QrcodeTag'),
                                    icon: 'fa fa-qrcode',
                                    extend: 'data-area=\'["350px", "380px"]\'',
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    url: function(row) {
                                        return 'equipment/equipment/qrcode?coding=' + row.coding + '&equipment_code=' + row.equipment_code;
                                    },
                                },
                            ], formatter: Table.api.formatter.buttons, operate: false
                        },
                        {field: 'archive.model', title: __('EquipmentModel')},
                        {field: 'archive.name', title: __('EquipmentName')},
                        {field: 'archive.load_supplier.name', title: __('SupplierId')},
                        {field: 'archive.purchasetime_text', title: __('Purchasetime')},
                        {field: 'archive.region', title: __('Region'), operate: false},
                        {field: 'archive.load_responsible_user.nickname', title: __('ResponsibleUid'), formatter: function(value, row){return value + '，' + row.archive.load_responsible_user.mobile}, operate: false},
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
        },
    };
    return Controller;
});

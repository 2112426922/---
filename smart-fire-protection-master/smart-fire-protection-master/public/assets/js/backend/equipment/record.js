define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'equipment/record/index' + location.search,
                    add_url: 'equipment/record/add',
                    edit_url: 'equipment/record/edit',
                    del_url: 'equipment/record/del',
                    multi_url: 'equipment/record/multi',
                    import_url: 'equipment/record/import',
                    table: 'equipment_record',
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
                        {field: 'equipment_id', title: __('Equipment_id')},
                        {field: 'relate_id', title: __('Relate_id')},
                        {field: 'add_uid', title: __('Add_uid')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'type', title: __('Type'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
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
                url: 'equipment/record/recyclebin' + location.search,
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
                                    url: 'equipment/record/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'equipment/record/destroy',
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
                url: 'equipment/Record/list' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {field: 'equipment.equipment_code', title: __('EquipmentCode'), operate: false},
                        {field: 'equipment.archive.model', title: __('EquipmentModel'), operate: false},
                        {field: 'equipment.archive.name', title: __('EquipmentName'), operate: false},
                        {field: 'name', title: __('RecordName'), operate: 'LIKE'},
                        {
                            field: 'createtime',
                            title: __('RecordTime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {field: 'user.nickname', title: __('RecordUser'), operate: false,
                            formatter : function(value, row){
                                if (row.add_uid === 0) {
                                    return '系统管理员';
                                }
                                return value;
                            }
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'qrcode',
                                    title: __('QrcodeTag'),
                                    icon: 'fa fa-qrcode',
                                    extend: 'data-area=\'["350px", "380px"]\'',
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    url: function(row) {
                                        return 'equipment/equipment/qrcode?coding=' + row.equipment.coding + '&equipment_code=' + row.equipment.equipment_code;
                                    },
                                },
                                {
                                    name: 'View',
                                    text: __('View'),
                                    title: __('View'),
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    url: 'equipment/Record/detail',
                                    visible: function (row) {
                                        if (row.add_uid > 0) {
                                            return true;
                                        }
                                    }
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ],
                search: false
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

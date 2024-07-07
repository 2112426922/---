define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.columnDefaults.align = 'left';
            Table.api.init({
                extend: {
                    index_url: 'equipment/archive/index' + location.search,
                    add_url: 'equipment/archive/add',
                    edit_url: 'equipment/archive/edit',
                    del_url: 'equipment/archive/del',
                    multi_url: 'equipment/archive/multi',
                    import_url: 'equipment/archive/import',
                    table: 'equipment_archive',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 2,
                columns: [
                    [
                        {checkbox: true, align: 'center'},
                        {field: 'id', title: __('Id'), align: 'center', operate: false},
                        {field: 'model', title: __('Model'), operate: 'LIKE'},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'parameter', title: __('Parameter'), operate: false,
                            formatter : function(value, row, index, field){
                                return "<span style='display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;' title='" + row.parameter + "'>" + value + "</span>";
                            },
                            cellStyle : function(value, row, index, field){
                                return {
                                    css: {
                                        "white-space": "nowrap",
                                        "text-overflow": "ellipsis",
                                        "overflow": "hidden",
                                        "max-width": "200px"
                                    }
                                };
                            }
                        },
                        {field: 'supplier.name', title: __('Supplier_id')},
                        {field: 'purchasetime_text', title: __('Purchasetime'), operate: false},
                        {field: 'purchasetime', title: __('Purchasetime'), visible: false, operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime},
                        {field: 'amount', title: __('Amount'), table: table, buttons: [
                            {
                                name: 'equipments',
                                text: function (row) {
                                    return row.amount + '台 ' + __('View')
                                },
                                title: __('Equipment List'),
                                extend: 'data-area=\'["100%", "100%"]\'',
                                classname: 'btn-dialog',
                                url: 'equipment/equipment?archive_id={id}'},
                        ], formatter: Table.api.formatter.buttons, operate: false},
                        {field: 'region', title: __('Region'), operate: 'LIKE'},
                        {field: 'responsible_user.nickname', title: __('Responsible_uid'), formatter: function(value, row){return value + '，' + row.responsible_user.mobile}, operate: false},
                        {field: 'document', title: __('Document'), table: table, buttons: [
                            {
                                name: 'documents',
                                text: __('View'),
                                title: __('View'),
                                icon: 'fa fa-eye',
                                extend: 'data-area=\'["100%", "100%"]\'',
                                classname: 'btn btn-xs btn-primary btn-dialog',
                                url: function (row) {
                                    return row.document;
                                },
                                visible: function (row) {
                                    return row.document !== '';
                                }
                            },
                        ], formatter: Table.api.formatter.buttons, operate: false},
                        {field: 'remark', title: __('Remark'), operate: false},
                        {field: 'record', title: __('Record'), table: table, buttons: [
                            {
                                name: 'repairs',
                                title: __('Repair List'),
                                icon: 'fa fa-wrench',
                                extend: 'data-area=\'["100%", "100%"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/repair/index?archive_id={id}'
                            },
                            {
                                name: 'inspection',
                                title: __('Inspection List'),
                                icon: 'fa fa-recycle',
                                extend: 'data-area=\'["900px", "600px"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/record/list?type=inspection&archiveId={id}'
                            },
                            {
                                name: 'maintenance',
                                title: __('Maintenance List'),
                                icon: 'fa fa-clock-o',
                                extend: 'data-area=\'["900px", "600px"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/record/list?type=maintenance&archiveId={id}'
                            }
                        ], formatter: Table.api.formatter.buttons, operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search: false
            });

            // 导出标签数据
            $(document).on("click", ".btn-export-tag", function () {
                var ids = Table.api.selectedids(table);
                top.location.href = "archive/exportTag?ids=" + ids.join(",");
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
                url: 'equipment/archive/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'model', title: __('Model'), operate: 'LIKE'},
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
                                    url: 'equipment/archive/restore',
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

        add: function () {
            // 员工选择弹窗
            $(document).on("click", ".btn-choose-staff", function () {
                Fast.api.open('equipment/staff/picker?parent_type=archive', __('Choose_staff'), {
                    callback: function(value){
                        var user_id = value.user_id;
                        var staff_list = Config.staffList;
                        $('#c-responsible_uid').val(user_id);
                        $('.responsible_name').val(staff_list[value.user_id]);
                    }
                });
            });

            Controller.api.bindevent();
        },
        edit: function () {
            // 员工选择弹窗
            $(document).on("click", ".btn-choose-staff", function () {
                Fast.api.open('equipment/staff/picker?parent_type=archive', __('Choose_staff'), {
                    callback: function(value){
                        var user_id = value.user_id;
                        var staff_list = Config.staffList;
                        $('#c-responsible_uid').val(user_id);
                        $('.responsible_name').val(staff_list[value.user_id]);
                    }
                });
            });

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
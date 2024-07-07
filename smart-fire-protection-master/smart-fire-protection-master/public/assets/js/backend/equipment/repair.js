define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.columnDefaults.align = 'left';
            Table.api.init({
                extend: {
                    index_url: 'equipment/repair/index' + location.search,
                    add_url: 'equipment/repair/add',
                    edit_url: 'equipment/repair/edit',
                    del_url: 'equipment/repair/del',
                    multi_url: 'equipment/repair/multi',
                    import_url: 'equipment/repair/import',
                    table: 'equipment_repair',
                    detail_url: 'equipment/repair/detail',
                    register_url: 'equipment/repair/register',
                    failure_cause_url: 'equipment/failure_cause/index',
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
                        // {checkbox: true, align: 'center'},
                        {field: 'id', title: __('Id'), align: 'center', operate: false},
                        {field: 'repair_code', title: __('Repair_code'), operate: 'LIKE', formatter: function (value) {
                            return (value === "" || value === null || value === undefined ) ? '-' : value;
                        }},
                        {field: 'archive.model', title: __('Model'), operate: 'LIKE'},
                        {field: 'archive.name', title: __('Name'), operate: 'LIKE'},
                        {field: 'equipment.equipment_code', title: __('Equipment_code'), operate: 'LIKE'},
                        {field: 'register_user.nickname', title: __('Register_uid')},
                        {field: 'registertime', title: __('Registertime'), operate: 'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY年MM月DD日 HH:mm"},
                        {field: 'assigntime', title: __('Assigntime'), operate: 'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY年MM月DD日 HH:mm"},
                        {field: 'failure_cause.name', title: __('Failure_cause'), operate: false},
                        {field: 'repair_user.nickname', title: __('Repair_uid')},
                        {field: 'repairtime', title: __('Repairtime'), operate: 'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY年MM月DD日 HH:mm"},
                        {field: 'status', title: __('Status'), searchList: {'pending': __('Pending'), 'registered': __('Registered'), 'repaired': __('Repaired'), 'scrapped': __('Scrapped')}, custom: {'pending': 'warning', 'registered': 'info', 'repaired': 'success', 'scrapped': 'danger'}, formatter:Table.api.formatter.label},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
                            {
                                name: 'assignment',
                                text: __('Assignment'),
                                title: __('Assignment_staff'),
                                icon: 'fa fa-hand-pointer-o',
                                classname: 'btn btn-xs btn-warning btn-dialog',
                                url: 'equipment/staff/picker?parent_id={id}&parent_type=repair',
                                visible: function (row) {
                                    if (row.status === 'pending' || row.status === 'registered') {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }
                            },
                            {
                                name: 'registers',
                                text: __('Register'),
                                title: __('Repair_content'),
                                icon: 'fa fa-pencil',
                                classname: 'btn btn-xs btn-success btn-dialog',
                                url: 'equipment/repair/register?id={id}',
                                visible: function (row) {
                                    if (row.status === 'registered') {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }
                            },
                            {
                                name: 'details',
                                text: __('View'),
                                title: __('Repair_detail'),
                                icon: 'fa fa-eye',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/repair/detail?id={id}'
                            },
                        ], events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search: false
            });

            // 故障原因管理弹窗
            $(document).on("click", ".btn-failure-cause", function () {
                Fast.api.open('equipment/failure_cause/index', __('Failure_cause'));
            });

            // 派单通知人员管理弹窗
            $(document).on("click", ".btn-reminder-users", function () {
                Fast.api.open('equipment/reminder_users/index?type=assign_repair', __('Reminder_users'));
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
                url: 'equipment/repair/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
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
                                    url: 'equipment/repair/restore',
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
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        assignment: function () {
            Controller.api.bindevent();
        },
        register: function () {
            Controller.api.bindevent();
        },
        detail: function () {
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
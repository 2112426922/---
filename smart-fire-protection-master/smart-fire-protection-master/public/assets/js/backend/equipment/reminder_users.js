define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'equipment/reminder_users/index' + location.search,
                    add_url: 'equipment/reminder_users/add',
                    edit_url: 'equipment/reminder_users/edit',
                    del_url: 'equipment/reminder_users/del',
                    multi_url: 'equipment/reminder_users/multi',
                    import_url: 'equipment/reminder_users/import',
                    table: 'equipment_reminder_users',
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
                        // {field: 'id', title: __('Id')},
                        {field: 'multi_staff.workno', title: __('Workno'), formatter: function (value) {
                            return (value === "" || value === null || value === undefined ) ? '-' : value;
                        }},
                        {field: 'multi_staff.multi_user.nickname', title: __('Nickname')},
                        {field: 'multi_staff.multi_user.mobile', title: __('Mobile')},
                        {field: 'multi_staff.multi_department.name', title: __('Department')},
                        {field: 'multi_staff.position', title: __('Position'), formatter: function (value) {
                            return (value === "" || value === null || value === undefined ) ? '-' : value;
                        }},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                    ]
                ],
                search: false,
                showToggle: false,
                showColumns: false,
                showExport: false,
                commonSearch: false
            });

            // 员工选择弹窗
            $(document).on("click", ".btn-choose-staff", function () {
                Fast.api.open('equipment/staff/picker?parent_type=assign_remind', __('Choose_staff'));
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
                url: 'equipment/reminder_users/recyclebin' + location.search,
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
                                    url: 'equipment/reminder_users/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'equipment/reminder_users/destroy',
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

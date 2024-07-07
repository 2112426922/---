define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.columnDefaults.align = 'left';
            Table.api.init({
                extend: {
                    index_url: 'equipment/staff/index' + location.search,
                    add_url: 'equipment/staff/add',
                    edit_url: 'equipment/staff/edit',
                    del_url: 'equipment/staff/del',
                    multi_url: 'equipment/staff/multi',
                    import_url: 'equipment/staff/import',
                    table: 'equipment_staff',
                    department_url: 'equipment/department/index',
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
                        {checkbox: true, align: 'center'},
                        {field: 'id', title: __('Id'), align: 'center', operate: false},
                        {field: 'workno', title: __('Workno'), operate: 'LIKE', formatter: function (value) {
                            return (value === "" || value === null || value === undefined ) ? '-' : value;
                        }},
                        {field: 'user.nickname', title: __('Nickname'), operate: 'LIKE'},
                        {field: 'user.mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'department.name', title: __('Department'), operate: 'LIKE'},
                        {field: 'position', title: __('Position'), operate: 'LIKE', formatter: function (value) {
                            return (value === "" || value === null || value === undefined ) ? '-' : value;
                        }},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table,
                            buttons: [
                                {
                                    name: 'unbind',
                                    title: __('Unbind') + __('Weapp'),
                                    icon: 'fa fa-chain-broken',
                                    confirm: __('Confirm to unbind Weapp?'),
                                    classname: 'btn btn-xs btn-warning btn-ajax',
                                    url: 'equipment/staff/unbind',
                                    refresh: true,
                                    visible: function (row) {
                                        if (row.openid !== '') {
                                            return true;
                                        }
                                    }
                                }
                            ],
                            events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search: false
            });

            // 部门管理弹窗
            $(document).on("click", ".btn-department-manage", function () {
                Fast.api.open('equipment/department/index', __('DepartmentManage'));
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.columnDefaults.align = 'left';
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'equipment/staff/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true, align: 'center'},
                        {field: 'id', title: __('Id'), align: 'center'},
                        {field: 'workno', title: __('Workno'), formatter: function (value) {
                            return (value === "" || value === null || value === undefined ) ? '-' : value;
                        }},
                        {field: 'user.nickname', title: __('Nickname')},
                        {field: 'user.mobile', title: __('Mobile')},
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
                                    url: 'equipment/staff/restore',
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
        picker: function () {
            // 初始化表格参数配置
            Table.columnDefaults.align = 'left';
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");
            var parentId = Config.parent_id;
            var parentType = Config.parent_type;
            var confirmContent = parentType === 'repair' ? '确认派单给该员工？' : '确认选择？';

            // 初始化表格
            table.bootstrapTable({
                url: 'equipment/staff/picker' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Choose',
                                    text: __('Choose'),
                                    classname: 'btn btn-xs btn-info btn-ajax',
                                    url: 'equipment/staff/pickerDeal?id={id}&user_id={user.id}&parent_id=' + parentId + '&parent_type=' + parentType,
                                    confirm: confirmContent,
                                    success: function (data) {
                                        Fast.api.close(data);
                                        parent.$("a.btn-refresh").trigger("click");
                                    },
                                    error: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        },
                        {field: 'workno', title: __('Workno'), operate: 'LIKE', formatter: function (value) {
                            return (value === "" || value === null || value === undefined ) ? '-' : value;
                        }},
                        {field: 'user.nickname', title: __('Nickname'), operate: 'LIKE'},
                        {field: 'user.mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'department.name', title: __('Department'), operate: 'LIKE'},
                        {field: 'position', title: __('Position'), operate: 'LIKE', formatter: function (value) {
                            return (value === "" || value === null || value === undefined ) ? '-' : value;
                        }},
                    ]
                ],
                search: false,
                showToggle: false,
                showColumns: false,
                showExport: false,
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
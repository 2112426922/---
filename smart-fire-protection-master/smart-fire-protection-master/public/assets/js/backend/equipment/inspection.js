define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'equipment/inspection/index' + location.search,
                    add_url: 'equipment/inspection/add',
                    table: 'equipment_inspection',
                    deactivated_url: 'equipment/inspection/deactivated',
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
                        {field: 'id', title: __('Id'), align: 'center', operate: false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'plan_archives', title: __('ArchiveRange'), operate: false, formatter: function (value) {
                            var string = '';
                            value.map(function (item) {
                                string += '[' + item.archive.model + '] ' + item.archive.name + '<br>';
                            });
                            return string.slice(0, string.length-1);
                        }},
                        {
                            field: 'plan_fields', title: __('FieldName'), operate: false, table: table, buttons: [
                                {
                                    name: 'fields',
                                    text: function (row) {
                                        return row.plan_fields.length + ' 个巡检项';
                                    },
                                    title: __('FieldName'),
                                    extend: 'data-area=\'["900px", "600px"]\'',
                                    classname: 'btn-dialog',
                                    url: 'equipment/plan_field/fields?ids={id}'
                                },
                            ],
                            formatter: Table.api.formatter.buttons
                        },
                        {field: 'first_duetime', title: __('First_duetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY年MM月DD日"},
                        {field: 'last_duetime', title: __('Last_duetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY年MM月DD日"},
                        {field: 'periodicity', title: __('Periodicity'), operate: false, formatter: function (value) {return value + ' 天一次';}},
                        {field: 'plan_users', title: __('PlanUser'), operate: false, formatter: function (value) {
                            var string = '';
                            value.map(function (item) {
                                string += item.user.nickname + '，';
                            });
                            return string.slice(0, string.length-1);
                        }},
                        {field: 'operate', title: __('Operate'), table: table,
                            buttons: [
                                {
                                    name: 'stop',
                                    text: __('Stop'),
                                    title: __('Stop'),
                                    confirm: __('After deactivating the plan, the task for the next cycle is no longer valid, is deactivation confirmed?'),
                                    classname: 'btn btn-xs btn-danger btn-ajax',
                                    url: 'equipment/plan/stop',
                                    refresh: true
                                },
                                {
                                    name: 'archives',
                                    text: __('Equipment Details'),
                                    title: __('Equipment Details'),
                                    extend: 'data-area=\'["900px", "600px"]\'',
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    url: 'equipment/plan/list?ids={id}'
                                },
                                {
                                    name: 'list',
                                    text: __('Inspection record details'),
                                    title: __('Inspection record details'),
                                    extend: 'data-area=\'["900px", "600px"]\'',
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    url: 'equipment/record/list?type=inspection'
                                }
                            ],
                            events: Table.api.events.operate, formatter: Table.api.formatter.operate
                        }
                    ]
                ],
                search: false
            });

            // 已停用计划弹窗
            $(document).on("click", ".btn-deactivated", function () {
                Fast.api.open('equipment/inspection/deactivated', __('Deactivated'), {
                    area: ["100%", "100%"],
                });
            });

            // 清理无效任务
            $(document).on("click", ".btn-clear-invalid-task", function () {
                Layer.confirm("确认清理无效任务？", function(){
                    Layer.closeAll("dialog");
                    Fast.api.ajax("equipment/plan/clearInvalidTask");
                });
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        deactivated: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'equipment/inspection/deactivated' + location.search,
                    table: 'equipment_inspection',
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
                        {field: 'id', title: __('Id'), align: 'center', operate: false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'plan_archives', title: __('ArchiveRange'), operate: false, formatter: function (value) {
                                var string = '';
                                value.map(function (item) {
                                    string += '[' + item.archive.model + '] ' + item.archive.name + '<br>';
                                });
                                return string.slice(0, string.length-1);
                            }},
                        {
                            field: 'plan_fields', title: __('FieldName'), operate: false, table: table, buttons: [
                                {
                                    name: 'fields',
                                    text: function (row) {
                                        return row.plan_fields.length + ' 个巡检项';
                                    },
                                    title: __('FieldName'),
                                    extend: 'data-area=\'["900px", "600px"]\'',
                                    classname: 'btn-dialog',
                                    url: 'equipment/plan_field/fields?ids={id}'
                                },
                            ],
                            formatter: Table.api.formatter.buttons
                        },
                        {field: 'first_duetime', title: __('First_duetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY年MM月DD日"},
                        {field: 'last_duetime', title: __('Last_duetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY年MM月DD日"},
                        {field: 'periodicity', title: __('Periodicity'), operate: false, formatter: function (value) {return value + ' 天一次';}},
                        {field: 'plan_users', title: __('PlanUser'), operate: false, formatter: function (value) {
                                var string = '';
                                value.map(function (item) {
                                    string += item.user.nickname + '，';
                                });
                                return string.slice(0, string.length-1);
                            }},
                        {field: 'deletetime', title: __('StopTime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY年MM月DD日 HH:mm"},
                        {field: 'operate', title: __('Operate'), table: table,
                            buttons: [
                                {
                                    name: 'archives',
                                    text: __('Equipment Details'),
                                    title: __('Equipment Details'),
                                    extend: 'data-area=\'["900px", "600px"]\'',
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    url: 'equipment/plan/list?ids={id}'
                                },
                                {
                                    name: 'list',
                                    text: __('Inspection record details'),
                                    title: __('Inspection record details'),
                                    extend: 'data-area=\'["900px", "600px"]\'',
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    url: 'equipment/record/list?type=inspection'
                                }
                            ],
                            events: Table.api.events.operate, formatter: Table.api.formatter.operate
                        }
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
                url: 'equipment/inspection/recyclebin' + location.search,
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
                                    url: 'equipment/inspection/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'equipment/inspection/destroy',
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
            function updatePlanField(type, index, value) {
                var fields = $("#c-plan_fields").val();
                if(fields.length === 0) {
                    fields = [];
                } else {
                    fields = JSON.parse(fields);
                }
                if(type === 'add') {
                    fields.push(value);
                }
                if(type === 'edit') {
                    fields.splice(index, 1, value);
                }
                if(type === 'delete') {
                    fields.splice(index, 1);
                }

                $("#c-plan_fields").val(JSON.stringify(fields));
            }

            function editField(row, data) {
                var url = 'equipment/plan_field/edit?';
                var value = '';
                for (var key in data) {
                    if (key === 'type_text') {
                        continue;
                    }
                    value = data[key];
                    if (Array.isArray(value)) {
                        value = value.join("==");
                    }
                    url += key + '=' + encodeURI(value) + '&';
                }
                url = url.substr(0, url.length - 1);
                var options = {
                    shadeClose: false,
                    shade: [0.3, '#393D49'],
                    area: ['500px', '600px'],
                    callback: function(value){
                        updatePlanField('edit', row.rowIndex, value);
                        $('#field-table').bootstrapTable('updateRow', {index: row.rowIndex, row: value});
                    }
                };
                parent.Fast.api.open(url, __('EditField'), options);
            }

            function deleteField(row, data) {
                Layer.confirm(
                    __('This operation will delete the inspection item, whether to continue?'),
                    {icon: 3, title: __('Warning'), shadeClose: true, btn: [__('OK'), __('Cancel')]},
                    function (index) {
                        updatePlanField('delete', row.rowIndex, []);
                        $('#field-table').bootstrapTable('removeByUniqueId', data.id);
                        Layer.close(index);
                    }
                );
            }

            var fieldTable = $("#field-table");
            fieldTable.bootstrapTable({
                uniqueId: 'id',
                columns: [{
                    field: 'label',
                    title: __('FieldName')
                }, {
                    field: 'type_text',
                    title: __('FieldType'),
                    formatter: Table.api.formatter.label
                }, {
                    field: 'default',
                    title: __('FieldDefault')
                }, {
                    field: 'require',
                    title: __('FieldRequire'),
                    searchList: {"0":__('NotRequire'), "1":__('IsRequire')},
                    formatter: Table.api.formatter.status
                }, {
                    field: 'operate',
                    title: __('Operate'),
                    table: fieldTable,
                    buttons: [
                        {
                            name: 'edit',
                            icon: 'fa fa-pencil',
                            title: __('Edit'),
                            classname: 'btn btn-xs btn-success btn-click',
                            click: function(row, data) {
                                editField(row, data);
                            },
                        },
                        {
                            name: 'delete',
                            icon: 'fa fa-trash',
                            title: __('Del'),
                            classname: 'btn btn-xs btn-danger btn-click',
                            click: function(row, data) {
                                deleteField(row, data);
                            },
                        }
                    ],
                    formatter: Table.api.formatter.buttons
                }]
            });

            $(document).on("click", ".add-field", function () {
                var options = {
                    shadeClose: false,
                    shade: [0.3, '#393D49'],
                    area: ['500px', '600px'],
                    callback: function(value){
                        updatePlanField('add', 0, value);
                        $('#field-table').bootstrapTable('append', value);
                    }
                };
                parent.Fast.api.open('equipment/plan_field/add', __('AddField'), options);
            });

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

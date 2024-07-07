define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'equipment/plan_field/index' + location.search,
                    add_url: 'equipment/plan_field/add',
                    edit_url: 'equipment/plan_field/edit',
                    del_url: 'equipment/plan_field/del',
                    multi_url: 'equipment/plan_field/multi',
                    import_url: 'equipment/plan_field/import',
                    table: 'equipment_plan_field',
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
                        {field: 'label', title: __('Label'), operate: 'LIKE'},
                        {field: 'name', title: __('Name')},
                        {field: 'type', title: __('Type')},
                        {field: 'default', title: __('Default')},
                        {field: 'sort', title: __('Sort')},
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

        fields: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'equipment/plan_field/fields' + location.search,
                    table: 'equipment_plan_field',
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
                        {field: 'label', title: __('FieldName'), operate: false},
                        {field: 'type_text', title: __('FieldType'), operate: false, formatter: Table.api.formatter.label},
                        {field: 'options', title: __('FieldOption'), operate: false, formatter: function (value, row) {
                            var typeArr = ['radio', 'multiple'];
                            if(typeArr.indexOf(row.type) === -1){
                                return '';
                            } else {
                                return unescape(value.replace(/\\u/g, '%u'));
                            }}
                        },
                        {field: 'default', title: __('FieldDefault'), operate: false},
                        {field: 'require', title: __('Require'), operate: false, searchList: {"0":__('NotRequire'), "1":__('IsRequire')}, formatter: Table.api.formatter.label}
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
                url: 'equipment/plan_field/recyclebin' + location.search,
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
                                    url: 'equipment/plan_field/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'equipment/plan_field/destroy',
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
            var _this = this;
            var fieldType = $('#field-type').val();
            _this.changeField(fieldType);

            $(document).on("change", "#field-type", function () {
                var fieldType = $('#field-type').val();
                _this.changeField(fieldType);
            });

            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);       //这里是重点
                // Toastr.success("成功");   //这个可有可无
            }, function(data, ret){
                // Toastr.success("失败");
            });

            // Controller.api.bindevent();
        },
        edit: function () {
            var _this = this;
            var fieldType = $('#field-type').val();
            _this.changeField(fieldType);

            $(document).on("change", "#field-type", function () {
                var fieldType = $('#field-type').val();
                _this.changeField(fieldType);
            });

            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);       //这里是重点
                // Toastr.success("成功");   //这个可有可无
            }, function(data, ret){
                // Toastr.success("失败");
            });

            // Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        },
        changeField: function (fieldType) {
            if(fieldType === 'image') {
                $('.field-default').hide();
                $('.field-options').hide();
            }
            if(fieldType === 'text' || fieldType === 'textarea' || fieldType === 'number') {
                $('.field-default').show();
                $('.field-options').hide();
            }
            if(fieldType === 'true-false' || fieldType === 'radio' || fieldType === 'multiple') {
                $('.field-default').hide();
                $('.field-options').show();
            }
        }
    };
    return Controller;
});

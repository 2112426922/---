define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.columnDefaults.align = 'left';
            Table.api.init({
                extend: {
                    index_url: 'equipment/supplier/index' + location.search,
                    add_url: 'equipment/supplier/add',
                    edit_url: 'equipment/supplier/edit',
                    del_url: 'equipment/supplier/del',
                    multi_url: 'equipment/supplier/multi',
                    import_url: 'equipment/supplier/import',
                    table: 'equipment_supplier',
                }
            });

            var table = $("#table");
            var relationshipList = [];
            $.ajax({
                url : "equipment/supplier/getRelationshipList",
                async : false,
                success : function (obj) {
                    relationshipList = obj.data;
                }
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true, align: 'center'},
                        {field: 'id', title: __('Id'), align: 'center', operate: false},
                        {field: 'supplier_code', title: __('Supplier_code'), operate: 'LIKE'},
                        {field: 'relationship', title: __('Relationship'), formatter: function(value){return relationshipList[value]}, searchList: relationshipList},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'bank', title: __('Invoice'), formatter: function(value, row){return value + '，' + row.bank_account}, operate: false},
                        {field: 'contact', title: __('Contact'), formatter: function(value, row){return value + '，' + row.contact_mobile}, operate: false},
                        {field: 'remark', title: __('Remark'), operate: false},
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
            Table.columnDefaults.align = 'left';
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'equipment/supplier/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true, align: 'center'},
                        {field: 'id', title: __('Id'), align: 'center'},
                        {field: 'supplier_code', title: __('Supplier_code')},
                        {field: 'name', title: __('Name')},
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
                                    url: 'equipment/supplier/restore',
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
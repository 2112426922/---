define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'screen/excel/index' + location.search,
                    add_url: 'screen/excel/add',
                    edit_url: 'screen/excel/edit',
                    del_url: 'screen/excel/del',
                    multi_url: 'screen/excel/multi',
                    import_url: 'screen/excel/import',
                    table: 'screen_excel',
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
                        {field: 'code', title: __('Code'), operate: 'LIKE'},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'desc', title: __('Desc'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Status normal'),"hidden":__('Status hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'viewer',
                                    text: '预览',
                                    classname: 'btn btn-primary btn-success btn-detail',
                                    extend: 'target="_blank"',
                                    url: '/assets/addons/screen#/excelreport/viewer?reportCode={code}',
                                },
                                {
                                    name: 'designer',
                                    text: '设计',
                                    classname: 'btn btn-primary btn-warning btn-detail',
                                    extend: 'target="_blank"',
                                    url: '/assets/addons/screen#/excelreport/designer?reportCode={code}',
                                },
                                {
                                    name: 'share',
                                    text: '分享',
                                    classname: 'btn btn-info btn-dialog',
                                    url: 'screen/share?excel_id={ids}',
                                }, {
                                    name: 'edit',
                                    icon: 'fa fa-pencil',
                                    title: __('Edit'),
                                    extend: 'data-toggle="tooltip"',
                                    classname: 'btn  btn-success btn-editone'
                                }, {
                                    name: 'del',
                                    icon: 'fa fa-trash',
                                    title: __('Del'),
                                    extend: 'data-toggle="tooltip"',
                                    classname: 'btn  btn-danger btn-delone'
                                },
                            ],
                        }
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
                url: 'screen/excel/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '140px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'screen/excel/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'screen/excel/destroy',
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

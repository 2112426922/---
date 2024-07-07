define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'screen/page/index' + location.search,
                    add_url: 'screen/page/add',
                    edit_url: 'screen/page/edit',
                    del_url: 'screen/page/del',
                    multi_url: 'screen/page/multi',
                    import_url: 'screen/page/import',
                    table: 'screen_page',
                }
            });

            var table = $("#table");

            Template.helper("Moment", Moment);

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                templateView: true,
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
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            // b***高级授权***
            this.importInit(table);
            // e***高级授权***
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
                url: 'screen/page/recyclebin' + location.search,
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
                                    url: 'screen/page/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'screen/page/destroy',
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

        // b***高级授权***
        // 导出初始化
        importInit: function (table) {
            require(['table'], function (Table) {
                var submitForm = function (ids, layero) {
                    var options = table.bootstrapTable('getOptions');
                    var columns = [];
                    $.each(options.columns[0], function (i, j) {
                        if (j.field && !j.checkbox && j.visible && j.field != 'operate') {
                            columns.push(j.field);
                        }
                    });
                    var search = options.queryParams({});
                    $("input[name=search]", layero).val(options.searchText);
                    $("input[name=ids]", layero).val(ids);
                    $("input[name=filter]", layero).val(search.filter);
                    $("input[name=op]", layero).val(search.op);
                    $("input[name=columns]", layero).val(columns.join(','));
                    $("form", layero).submit();
                };

                if ($.fn.bootstrapTable.defaults.extend.export_url) {
                    var url = $.fn.bootstrapTable.defaults.extend.export_url;
                } else {
                    var url = Config.controllername + '/' + 'export';
                }

                $(document).on("click", ".btn-export", function () {
                    var ids = Table.api.selectedids(table);
                    var page = table.bootstrapTable('getData');
                    var all = table.bootstrapTable('getOptions').totalRows;

                    if ($(this).data('url')) {
                        url = $(this).data('url');
                    }

                    var type = $(this).data('type');
                    if (!type) {
                        if (ids.length > 0) {
                            type = 3;
                        } else {
                            type = 2;
                        }
                    }

                    if (type == 1) {
                        Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl(url) + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
                            title: '导出数据',
                            btn: ["全部(" + all + "条)"],
                            success: function (layero, index) {
                                $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                            }
                            , yes: function (index, layero) {
                                submitForm("all", layero);
                                return false;
                            }
                        })
                    } else if (type == 2) {
                        Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl(url) + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
                            title: '导出数据',
                            btn: ["本页(" + page.length + "条)", "全部(" + all + "条)"],
                            success: function (layero, index) {
                                $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                            }
                            , yes: function (index, layero) {
                                var ids = [];
                                $.each(page, function (i, j) {
                                    ids.push(j.id);
                                });
                                submitForm(ids.join(","), layero);
                                return false;
                            }
                            ,
                            btn2: function (index, layero) {
                                submitForm("all", layero);
                                return false;
                            }
                        })
                    } else {
                        Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl(url) + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
                            title: '导出数据',
                            btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)", "全部(" + all + "条)"],
                            success: function (layero, index) {
                                $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                            }
                            , yes: function (index, layero) {
                                submitForm(ids.join(","), layero);
                                return false;
                            }
                            ,
                            btn2: function (index, layero) {
                                var ids = [];
                                $.each(page, function (i, j) {
                                    ids.push(j.id);
                                });
                                submitForm(ids.join(","), layero);
                                return false;
                            }
                            ,
                            btn3: function (index, layero) {
                                submitForm("all", layero);
                                return false;
                            }
                        })
                    }
                });
            });
        },
        //e***高级授权***//

        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});

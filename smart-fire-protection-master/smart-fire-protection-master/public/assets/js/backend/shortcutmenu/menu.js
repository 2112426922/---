define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {
    var Controller = {
        edit: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shortcutmenu.menu/index',
                    add_url: 'shortcutmenu.menu/add',
                    edit_url: 'shortcutmenu.menu/edit' + location.search,
                    del_url: 'shortcutmenu.menu/del',
                    multi_url: 'shortcutmenu.menu/multi',
                    import_url: 'shortcutmenu.menu/import',
                    table: 'shortcut_menu',
                }
            });

            var table = $("#table");


            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.edit_url,
                pk: 'id',
                sortName: 'auth_rule_id',
                escape: false,
                //pagination: false,
                singleSelect: true,
                showToggle: false,
                showColumns: false,
                pagination: false,
                showExport: false,
                commonSearch: false,
                columns: [
                    [
                        {field: 'state', checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left', formatter: Controller.api.formatter.title},
                        {
                            field: 'pid',
                            title: '<a href="javascript:;" class="btn btn-success btn-xs btn-toggle"><i class="fa fa-chevron-up"></i></a>',
                            operate: false,
                            formatter: Controller.api.formatter.subnode
                        },
                        {
                            field: 'menu_color',
                            title: '菜单背景色',
                            formatter: function (value, row, index) {

                                var selectOptions = '';

                                var selectData = Config.selectData

                                for (var i = 0; i < selectData.length; i++) {

                                    if (selectData[i].id === row.menu_color) {
                                        selectOptions += '<option class="' + selectData[i].id + '" value="' + selectData[i].id + '" selected>' + selectData[i].text + '</option>';
                                    } else {
                                        selectOptions += '<option class="' + selectData[i].id + '" value="' + selectData[i].id + '">' + selectData[i].text + '</option>';
                                    }


                                }

                                var selectHtml = '<select class="form-control menu_color ' + row.menu_color + '">' + selectOptions + '</select>';

                                return selectHtml;
                            },
                            events: {
                                "change .menu_color": function (e) {

                                    var selItem = $(this).val();
                                    if (selItem == $(this).find('option:first').val()) {

                                    } else {
                                        $(this).css("background-color", $(this).val());
                                    }

                                    var row = $('#table').bootstrapTable('getSelections');
                                    if (!row) {
                                        Layer.alert('请先选择要操作的菜单');
                                    }

                                    var id = row[0]['id'];

                                    $(this).data("params", {menu_color: $(this).val()});

                                    Table.api.multi('', id, table, this);

                                    return false;


                                }
                            }
                        },

                        {
                            field: 'is_shortcut_menu',
                            title: __('Is_shortcut_menu'),
                            formatter: Table.api.formatter.toggle
                        }
                    ]
                ]
            });

            //当内容渲染完成后
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                //默认隐藏所有子节点
                $("a.btn[data-id][data-pid][data-pid!=0]").closest("tr").hide();
                $(".btn-node-sub.disabled[data-pid!=0]").closest("tr").hide();

                //显示隐藏子节点
                $(".btn-node-sub").off("click").on("click", function (e) {
                    var status = $(this).data("shown") || $("a.btn[data-pid='" + $(this).data("id") + "']:visible").length > 0 ? true : false;
                    $("a.btn[data-pid='" + $(this).data("id") + "']").each(function () {
                        $(this).closest("tr").toggle(!status);
                        if (!$(this).hasClass("disabled")) {
                            $(this).trigger("click");
                        }
                    });
                    $(this).data("shown", !status);
                    return false;
                });

            });
            //展开隐藏一级
            $(document.body).on("click", ".btn-toggle", function (e) {
                $("a.btn[data-id][data-pid][data-pid!=0].disabled").closest("tr").hide();
                var that = this;
                var show = $("i", that).hasClass("fa-chevron-down");
                $("i", that).toggleClass("fa-chevron-down", !show);
                $("i", that).toggleClass("fa-chevron-up", show);
                $("a.btn[data-id][data-pid][data-pid!=0]").closest("tr").toggle(show);
                $(".btn-node-sub[data-pid=0]").data("shown", show);
            });
            //展开隐藏全部
            $(document.body).on("click", ".btn-toggle-all", function (e) {
                var that = this;
                var show = $("i", that).hasClass("fa-plus");
                $("i", that).toggleClass("fa-plus", !show);
                $("i", that).toggleClass("fa-minus", show);
                $(".btn-node-sub.disabled[data-pid!=0]").closest("tr").toggle(show);
                $(".btn-node-sub[data-pid!=0]").data("shown", show);
            });


            $(document).on("change", ".menu_color", function () {


            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        api: {
            formatter: {
                title: function (value, row, index) {
                    return row.pid == 'hidden' ? "<span class='text-muted'>" + value + "</span>" : value;
                },
                name: function (value, row, index) {
                    return row.pid == 'hidden' ? "<span class='text-muted'>" + value + "</span>" : value;
                },
                icon: function (value, row, index) {
                    return '<span class="' + (row.pis == 'hidden' ? 'text-muted' : '') + '"><i class="' + value + '"></i></span>';
                },
                subnode: function (value, row, index) {
                    return '<a href="javascript:;" data-toggle="tooltip" title="' + __('Toggle sub menu') + '" data-id="' + row.id + '" data-pid="' + row.pid + '" class="btn btn-xs '
                        + (row.haschild == 1 ? 'btn-success' : 'btn-default disabled') + ' btn-node-sub"><i class="fa fa-' + (row.haschild == 1 ? 'sitemap' : 'list') + '"></i></a>';
                },

            },
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});

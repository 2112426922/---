define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.columnDefaults.align = 'left';
            Table.api.init({
                extend: {
                    index_url: 'equipment/equipment/index' + location.search,
                    add_url: 'equipment/equipment/add',
                    edit_url: 'equipment/equipment/edit',
                    del_url: 'equipment/equipment/del',
                    multi_url: 'equipment/equipment/multi',
                    import_url: 'equipment/equipment/import',
                    table: 'equipment_equipment',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 3,
                columns: [
                    [
                        {checkbox: true, align: 'center'},
                        {field: 'id', title: __('Id'), align: 'center', operate: false},
                        {field: 'equipment_code', title: __('Equipment_code'), operate: false},
                        {field: 'archive.model', title: __('Model'), operate: false},
                        {field: 'archive.name', title: __('Name'), operate: false},
                        {field: 'archive.load_supplier.name', title: __('Supplier_id')},
                        {field: 'archive.purchasetime_text', title: __('Purchasetime'), operate: false},
                        {field: 'archive.region', title: __('Region'), operate: false},
                        {field: 'archive.load_responsible_user.nickname', title: __('Responsible_uid'), formatter: function(value, row){return value + '，' + row.archive.load_responsible_user.mobile}, operate: false},
                        {field: 'coding', title: __('Qrcode_tag'), table: table, buttons: [
                            {
                                name: 'qrcode',
                                text: '',
                                title: __('Qrcode_tag'),
                                icon: 'fa fa-qrcode',
                                extend: 'data-area=\'["350px", "380px"]\'',
                                classname: 'btn btn-xs btn-primary btn-dialog',
                                url: function(row) {
                                    return 'equipment/equipment/qrcode?coding=' + row.coding + '&equipment_code=' + row.equipment_code;
                                },
                            },
                        ], formatter: Table.api.formatter.buttons, operate: false},
                        {field: 'work_status_text', title: __('Work_status'), operate: false, custom: {'停机待修': 'info', '正常': 'success', '停机报废': 'danger'}, formatter:Table.api.formatter.label},
                        {field: 'record', title: __('Record'), table: table, buttons: [
                            {
                                name: 'monitoring',
                                title: __('设备监控'),
                                icon: 'fa fa-wpexplorer',
                                extend: 'data-area=\'["90%", "90%"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url:function(row){
                                    return 'http://192.168.137.132:8080/stream.html';
                                }
                            },
                            {
                                name: 'repairs',
                                title: __('Repair List'),
                                icon: 'fa fa-wrench',
                                extend: 'data-area=\'["100%", "100%"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/repair/index?equipment_id={id}'
                            },
                            {
                                name: 'inspection',
                                title: __('Inspection List'),
                                icon: 'fa fa-recycle',
                                extend: 'data-area=\'["900px", "600px"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/record/list?type=inspection&equipmentId={id}'
                            },
                            {
                                name: 'maintenance',
                                title: __('Maintenance List'),
                                icon: 'fa fa-clock-o',
                                extend: 'data-area=\'["900px", "600px"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/record/list?type=maintenance&equipmentId={id}'
                            },
                            {
                                name: 'message',
                                title: __('Massage List'),
                                icon: 'fa fa-list-alt',
                                extend: 'data-area=\'["100%", "100%"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/message/index?code={equipment_code}'
                            },
                            {
                                name: 'caution',
                                title: __('Caution'),
                                icon: 'fa fa-bell',
                                extend: 'data-area=\'["100%", "100%"]\'',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                url: 'equipment/caution/index?code={equipment_code}'
                            }
                        ], formatter: Table.api.formatter.buttons, operate: false},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search: false,
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
                url: 'equipment/equipment/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'equipment_code', title: __('Equipment_code'), operate: false},
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
                                    url: 'equipment/equipment/restore',
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
        qrcode: function () {
            var dataURLtoBlob = function (dataUrl) {
                var arr = dataUrl.split(","),
                    mime = arr[0].match(/:(.*?);/)[1],
                    bStr = atob(arr[1]),
                    n = bStr.length,
                    u8arr = new Uint8Array(n);
                while (n--) {
                    u8arr[n] = bStr.charCodeAt(n);
                }
                return new Blob([u8arr], { type: mime });
            };
            var downloadFile = function (url, name) {
                var a = document.createElement("a");
                a.setAttribute("href", url);
                a.setAttribute("download", name);
                a.setAttribute("target", "_blank");
                var clickEvent = document.createEvent("MouseEvents");
                clickEvent.initEvent("click", true, true);
                a.dispatchEvent(clickEvent);
            };
            var downloadFileByBase64 = function (base64, name) {
                var myBlob = dataURLtoBlob(base64);
                var myUrl = URL.createObjectURL(myBlob);
                downloadFile(myUrl, name);
            };

            $(document).on("click", ".btn-download", function () {
                var base64 = $("#qrcode-url").attr("src");
                var name = $(".btn-download").data("name");
                downloadFileByBase64(base64, name);
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
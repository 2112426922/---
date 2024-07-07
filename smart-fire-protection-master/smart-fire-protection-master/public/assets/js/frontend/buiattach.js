define(['jquery', 'bootstrap', 'frontend', 'form', 'table'], function ($, undefined, Frontend, Form, Table) {
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'buiattach/index',
                    table: 'attachment'
                },
            });
            var table = $("#table");
            Template.helper("Moment", Moment);
			let domain = window.location.protocol + '//'+ document.location.host + Config.moduleurl;

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                formatshowingRows:false,
                paginationDetail:false,
                sortName: 'id',
                templateView: true,
                columns: [
                    [
                        {field: 'state', checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'category', title: __('Category'), operate: 'in', formatter: Table.api.formatter.label, searchList: Config.categoryList},
                        {field: 'preview', title: __('Preview'), formatter: Controller.api.formatter.thumb, operate: false},
                        {field: 'url', title: __('Url'), formatter: Controller.api.formatter.url, visible: false},
                        {field: 'filename', title: __('Filename'), sortable: true, formatter: Controller.api.formatter.filename, operate: 'like'},
                        {field: 'filesize', title: __('Filesize'), operate: 'BETWEEN', sortable: true, formatter: function (value, row, index) {
                                var size = parseFloat(value);
                                var i = Math.floor(Math.log(size) / Math.log(1024));
                                return (size / Math.pow(1024, i)).toFixed(i < 2 ? 0 : 2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i];
						}}
                    ]
                ],
                //禁用默认搜索
                search: false,
                //启用普通表单搜索
                commonSearch: false,
                //可以控制是否默认显示搜索单表,false则隐藏,默认为false
                searchFormVisible: false,
                //分页大小
                pageSize: 12
                
            });

            // 绑定过滤事件
            $('.filter-type a', table.closest(".panel-intro")).on('click', function (e) {
                $(this).parent().find("a").removeClass("active");
                $(this).addClass("active");
                var value = $(this).data("value") || '';
				var query = {};
				if(value.length > 0){
					query = {category:value};
				}
                table.bootstrapTable('refresh',{silent:true,query:query});
            });
								
            //移动至
			$(document).on("click", ".dropdown-menu li a", function () {
				var chk_value = [];
				var category  = $(this).data("value");
				$('input[name="checkbox"]:checked').each(function(){
					chk_value.push($(this).val());
				});
				
				if(chk_value.length < 1){
					Toastr.error("请选择需要移动至的图片");
					return false;
				}
                Fast.api.ajax({
                    url: domain+"/buiattach/classify",
                    type: "post",
                    data: {category: category, ids: chk_value.join(',')},
                }, function () {
                    table.bootstrapTable('refresh', {});
                    Layer.close(index);
                });
			});
			
            //图片点击事件
            $(document).on("click", ".mask", function () {
				var multiple = $("input[name='multiple']").val() || '0';
				var select_value = $(this).data("select") || '0';	
				if(multiple == "0" && select_value == "0"){
					$(".thumbnail").each(function(){
						$(this).find(".mask").css({"opacity":"0"});
						$(this).find(".mask").data("select","0");
						$(this).find("input[type='checkbox']").prop('checked',false);
					});
				}	
				if(select_value == "0"){
					$(this).css({"opacity":"1"});
					$(this).data("select","1");
					$(this).parent().find("input[type='checkbox']").prop('checked',true);
				}
				if(select_value == "1"){
					$(this).css({"opacity":"0"});
					$(this).data("select","0");
					$(this).parent().find("input[type='checkbox']").prop('checked',false);
				}				
            });
			
			//搜索事件
			$(document).on("click",".glyphicon-search",function(){
				var filename = $("#filename").val();
				var query = {filename:filename};
				table.bootstrapTable('refresh',{silent:true,query:query});
			});
			
			// 为表格绑定事件
            Table.api.bindevent(table);
			require(['upload'], function (Upload) {
                Upload.api.upload($("#toolbar .faupload"), function () {
					table.bootstrapTable('refresh', {});
                });
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                thumb: function (value, row, index) {
                    var html = '';
                    if (row.mimetype.indexOf("image") > -1) {
                        html = '<a href="' + row.fullurl + '" target="_blank"><img src="' + row.fullurl + row.thumb_style + '" alt="" style="max-height:60px;max-width:120px"></a>';
                    } else {
                        html = '<a href="' + row.fullurl + '" target="_blank"><img src="' + Fast.api.fixurl("ajax/icon") + "?suffix=" + row.imagetype + '" alt="" style="max-height:90px;max-width:120px"></a>';
                    }
                    return '<div style="width:120px;margin:0 auto;text-align:center;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;">' + html + '</div>';
                },
                url: function (value, row, index) {
                    return '<a href="' + row.fullurl + '" target="_blank" class="label bg-green">' + row.url + '</a>';
                },
                filename: function (value, row, index) {
                    return '<div style="width:150px;margin:0 auto;text-align:center;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;">' + Table.api.formatter.search.call(this, value, row, index) + '</div>';
                },
                mimetype: function (value, row, index) {
                    return '<div style="width:80px;margin:0 auto;text-align:center;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;">' + Table.api.formatter.search.call(this, value, row, index) + '</div>';
                }
            }
        }

    };
    return Controller;
});
define(['jquery', 'bootstrap', 'backend', 'form', 'table'], function ($, undefined, Backend, Form, Table) {
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

			//判断浏览器大小
			function screen() {
				var width = top.document.documentElement.clientWidth;
				if (width > 1200) {
					return 3;   //大屏幕
				} else if (width > 992) {
					return 2;   //中屏幕
				} else if (width > 768) {
					return 1;   //小屏幕
				} else {
					return 0;   //超小屏幕
				}
			}
			
            // 绑定过滤事件
            $('.filter-type a', table.closest(".panel-intro")).on('click', function (e) {
                $(this).parent().find("a").removeClass("active");
                $(this).addClass("active");
                var value = $(this).data("value") || '';
				var query = {};
				if(value.length > 0){
					query = {
                        op: JSON.stringify({"category":"IN"}),
                        filter:JSON.stringify({"category":value})
                    };
				}
                table.bootstrapTable('refresh',{silent:true,query:query});
            });
					
            //附件归类
            $(document).on('click', '.btn-classify', function () {
                var ids = Table.api.selectedids(table);
                Layer.open({
                    title: __('Classify'),
                    content: Template("typetpl",{}),
                    yes: function (index, layero) {
                        var category = $("select[name='category']", layero).val();
                        Fast.api.ajax({url: "general/attachment/classify",type: "post",
                            data:{category:category,ids:ids.join(',')},
                        }, function () {
                            table.bootstrapTable('refresh', {});
                            Layer.close(index);
                        });
                    },
                    success: function (layero, index) {}
                });
            });	

			//图片删除事件
			$(document).on("click","#delImage",function(){
				var chk_value = [];
				var category  = $(this).data("value");
				$('input[name="checkbox"]:checked').each(function(){
					chk_value.push($(this).val());
				});
				if(chk_value.length < 1){
					Toastr.error("请先选中图片在删除");
					return false;
				}
				var image_id = chk_value.join(",");
                Layer.confirm(__('Are you sure you want to delete this item?'), {icon: 3,title: '提示'}, function (index) {
                    Backend.api.ajax({
                        url: "general/attachment/del",
                        data: {ids:image_id}
                    }, function () {
                        table.bootstrapTable('refresh', {});
                        Layer.close(index);
                    });
                });
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
                    url: "general/attachment/classify",
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
				var query = {op: JSON.stringify({"filename":"LIKE"}),filter:JSON.stringify({"filename":filename})};
				table.bootstrapTable('refresh',{silent:true,query:query});
			});
			//添加分类事件
			$(document).on("click","#addCategory",function(){
				//控制窗体大小
				var area = ['50%', '70%']; //大屏幕
				//越小屏幕占用空间越大
				if(screen() == 2){
					area = ['80%', '90%'];
				}else if(screen() == 1){
					area = ['80%', '90%'];
				}else if(screen() == 0){
					area = ['80%', '90%'];
				}
                Layer.open({
                    title: __('分类管理'),
					type : 2,
                    content: "category",
					area: area,
                    yes: function (index, layero) {},
                    success: function (layero, index) {}
                });
			});
			// 为表格绑定事件
            Table.api.bindevent(table);
			Controller.api.selectInit();
			require(['upload'], function (Upload) {
                Upload.api.upload($("#toolbar .faupload"), function () {
					      table.bootstrapTable('refresh', {});
                });
            });
        },
		category:function(){
			$(document).on("click","#submit",function(){
				var category = $("textarea[name='row[attachmentcategory]']").val();
				var token = $("input[name='__token__").val();
				Backend.api.ajax({
                    url: "general/config/edit",
                    data: {'row[attachmentcategory]':category,'__token__':token}
                }, function (res) {
					Layer.close();
                    window.parent.location.reload();
                });
				return false;
			});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {});
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
                },
            },
			selectInit:function(){
				//追加控制
                $(document).on("click", ".btn-append,.append", function (e, row) {
                    var container = $(this).closest(".fieldlist");
                    var tagName = container.data("tag") || (container.is("table") ? "tr" : "dd");
                    var index = container.data("index");
                    var name = container.data("name");
                    var template = container.data("template");
                    var data = container.data();
                    index = index ? parseInt(index) : 0;
                    container.data("index", index + 1);
                    row = row ? row : {};
                    var vars = {index: index, name: name, data: data, row: row};
                    var html = template ? Template(template, vars) : Template.render(Form.config.fieldlisttpl, vars);
                    $(html).attr("fieldlist-item", true).insertBefore($(tagName + ":last", container));
                    $(this).trigger("fa.event.appendfieldlist", $(this).closest(tagName).prev());
                });
				 //移除控制
                $(document).on("click", ".btn-remove", function () {
                    var container = $(this).closest(".fieldlist");
                    var tagName = container.data("tag") || (container.is("table") ? "tr" : "dd");
                    $(this).closest(tagName).remove();
                    refresh(container.data("name"));
                });
			}
        }

    };
    return Controller;
});
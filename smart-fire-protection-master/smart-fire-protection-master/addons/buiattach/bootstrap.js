/**
 * 图片选择器 - CODE 
 */
require(['form', 'upload'], function (Form, Upload) {
	Form.events.bindevent = function (form) {
		if(typeof(Config.buiattach.is_default) !== "undefined" && Config.buiattach.is_default=="2"){
			Form.events.faselect = function(form){
				if ($(".faselect,.fachoose", form).length > 0) {
					$(".faselect,.fachoose", form).off('click').on('click', function () {
						var that = this;
						var multiple = $(this).data("multiple") ? $(this).data("multiple") : false;
						var admin_id = $(this).data("admin-id") ? $(this).data("admin-id") : '';
						var user_id = $(this).data("user-id") ? $(this).data("user-id") : '';
						var mimetype = $(this).data("mimetype") ? $(this).data("mimetype") : '';
						mimetype = mimetype.replace(/\/\*/ig, '');
						if(mimetype=='image'){
							//初始配置
							var cdnurl   = Config.upload.cdnurl;
							let domain   = window.location.protocol + '//'+ document.location.host + Config.moduleurl;
							var button   = $("#" + $(that).attr("id"));
							var imgcount = $(button).data("maxcount");
							var inputval = $(button).data("input-id") ? $(button).data("input-id") : "";
							var multiple = (multiple == true) ? 1 : 0;
							var content  = domain+'/buiattach/index?multiple=' + multiple;
							var preview  = Upload.config.previewtpl;
						
							//图片选择最大数处理
							if(typeof(imgcount) == "undefined"){
								imgcount = 0; //不限制
							}else{
								imgcount = parseInt(imgcount);
							}
				
							//控制窗体大小
							var area = ['65%', '80%']; //大屏幕
							
							//越小屏幕占用空间越大
							if(screen() == 2){
								area = ['80%', '90%'];
							}else if(screen() == 1){
								area = ['100%', '100%'];
							}else if(screen() == 0){
								area = ['100%', '100%'];
							}
							
							top.Layer.open({
								type:2,
								title: __('图片选择器'),
								content: content,
								area: area,
								maxmin:false,
								shade: 0.5,
								btn: ['确定','取消'],
								btnAlign: "r",
								yes: function (index, layero) {
									var html = '';
									var arrs = [];
									var full = [];
									var defs = [];
									var body = top.layer.getChildFrame('body',index);
									var getvalue = $("#"+inputval).val(); //取值
									if(typeof(getvalue) == "undefined"){
										getvalue = "";
									}
									if(multiple == 1 && getvalue.length > 1){
										defs = getvalue.split(',');
									}
									body.find("input[type='checkbox']:checked").each(function(){
										var src = $(this).parent().find(".picture").attr("data-src");
										if(multiple == 1 && defs.length > 0){
											var indexs  = $.inArray(src,defs);
											if(indexs == "-1"){
												full.push($(this).parent().find(".picture").attr("src"));
												arrs.push($(this).parent().find(".picture").attr("data-src"));
											}
										}else{
											full.push($(this).parent().find(".picture").attr("src"));
											arrs.push($(this).parent().find(".picture").attr("data-src"));
										}
									});

									//多图 设置图片数量大于0  
									if(multiple == 1 && imgcount > 0){
										var curr_count = arrs.concat(defs);
										if(curr_count.length > imgcount){
											top.layer.msg("图片不能大于"+imgcount+"张", {icon: 0});
											return false;
										}
									}
									arrs.forEach((imgstr, index) => {
										var preview_html = preview;
										preview_html = preview_html.replace("<%=url%>",imgstr);
										preview_html = preview_html.replace(/<%=fullurl%>/g,full[index]);
										preview_html = preview_html.replace("<%=suffix%>","FAIL");
										html += preview_html;
									});
									if(multiple == 0){
										$("#"+inputval).parent().parent().find("ul").html(html);
										$("#"+inputval).val(arrs.join(",")).trigger("change").trigger("validate");
										top.layer.close(index);
										return false;
									}
									if(multiple == 1 && defs.length > 0){
										arrs = arrs.concat(defs);
									}
									$("#"+inputval).parent().parent().find("ul").append(html);
									$("#"+inputval).val(arrs.join(",")).trigger("change").trigger("validate");
									top.layer.close(index);
								}
							});
						}else{
							var url = $(this).data("url") ? $(this).data("url") : (typeof Backend !== 'undefined' ? "general/attachment/select" : "user/attachment");
							parent.Fast.api.open(url + "?element_id=" + $(this).attr("id") + "&multiple=" + multiple + "&mimetype=" + mimetype + "&admin_id=" + admin_id + "&user_id=" + user_id, __('Choose'), {
								callback: function (data) {
									var button = $("#" + $(that).attr("id"));
									var maxcount = $(button).data("maxcount");
									var input_id = $(button).data("input-id") ? $(button).data("input-id") : "";
									maxcount = typeof maxcount !== "undefined" ? maxcount : 0;
									if (input_id && data.multiple) {
										var urlArr = [];
										var inputObj = $("#" + input_id);
										var value = $.trim(inputObj.val());
										if (value !== "") {
											urlArr.push(inputObj.val());
										}
										var nums = value === '' ? 0 : value.split(/\,/).length;
										var files = data.url !== "" ? data.url.split(/\,/) : [];
										$.each(files, function (i, j) {
											var url = Config.upload.fullmode ? Fast.api.cdnurl(j) : j;
											urlArr.push(url);
										});
										if (maxcount > 0) {
											var remains = maxcount - nums;
											if (files.length > remains) {
												Toastr.error(__('You can choose up to %d file%s', remains));
												return false;
											}
										}
										var result = urlArr.join(",");
										inputObj.val(result).trigger("change").trigger("validate");
									} else {
										var url = Config.upload.fullmode ? Fast.api.cdnurl(data.url) : data.url;
										$("#" + input_id).val(url).trigger("change").trigger("validate");
									}
								}
							});
						}
						return false;
					});
				}
			}
		}
		
		
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
		
		//图片选择器 - 添加
		$(document).on("click",".select-upload",function(){
			
			//初始配置
			var Config = requirejs.s.contexts._.config.config;
			let domain = window.location.protocol + '//'+ document.location.host + Config.moduleurl;
			var _this	 = this;
			var shape    = $(_this).attr("data-shape"); //图片形状
			var inputval = $(_this).attr("data-input"); //输入值
			var imgcount = $(_this).attr("data-maxcount"); //图片最大选择数
			var cdnurl   = Config.upload.cdnurl;
			var multiple = ($(_this).attr("data-multiple") == "true") ? 1 : 0;
			var content  = domain+'/buiattach/index?multiple=' + multiple;
			var width    = '100';
			var pleft    = '91px';
			
			if(typeof(shape) !== "undefined" && shape == "oblong"){
				width = '150';
				pleft = '141px'
			}
						
			if(typeof(inputval) == "undefined"){
				Toastr.error("属性data-input配置错误");
				return false;
			}
			
			if($(_this).parent().find("input[name='"+inputval+"']").length < 1){
				Toastr.error("属性data-input配置错误");
				return false;
			}
			
			
			//图片选择最大数处理
			if(typeof(imgcount) == "undefined"){
				imgcount = 0; //不限制
			}else{
				imgcount = parseInt(imgcount);
			}
			
			//控制窗体大小
			var area = ['65%', '80%']; //大屏幕
			//越小屏幕占用空间越大
			if(screen() == 2){
				area = ['80%', '90%'];
			}else if(screen() == 1){
				area = ['100%', '100%'];
			}else if(screen() == 0){
				area = ['100%', '100%'];
			}
			top.Layer.open({
				type:2,
				title: __('图片选择器'),
				content: content,
				area: area,
				maxmin:false,
				shade: 0.5,
				btn: ['确定','取消'],
				btnAlign: "r",
				yes: function (index, layero) {
					var html = '';
					var arrs = [];
					var full = [];
					var defs = [];
					var body = top.layer.getChildFrame('body',index);
					var getvalue = $(_this).parent().find("input[name='"+inputval+"']").val(); //取值
					
					if(typeof(getvalue) == "undefined"){
						getvalue = "";
					}
					
					if(multiple == 1 && getvalue.length > 1){
						defs = getvalue.split(',');
					}
					body.find("input[type='checkbox']:checked").each(function(){
						var src = $(this).parent().find(".picture").attr("data-src");
						if(multiple == 1 && defs.length > 0){
							var indexs  = $.inArray(src,defs);
							if(indexs == "-1"){
								full.push($(this).parent().find(".picture").attr("src"));
								arrs.push($(this).parent().find(".picture").attr("data-src"));
							}
						}else{
							full.push($(this).parent().find(".picture").attr("src"));
							arrs.push($(this).parent().find(".picture").attr("data-src"));
						}
					});

					//多图 设置图片数量大于0  
					if(multiple == 1 && imgcount > 0){
						var curr_count = arrs.concat(defs);
						if(curr_count.length > imgcount){
							top.layer.msg("图片不能大于"+imgcount+"张", {icon: 0});
							return false;
						}
						if(curr_count.length == imgcount){
							$(_this).hide();
						}
						
					}
					
					arrs.forEach((imgstr, index) => {
						html += '<div class="select-images" style="float:left;margin-right:4%;">';
						html += '<img src="'+full[index]+'" data-src="'+imgstr+'" onerror="this.src=\''+domain+'/ajax/icon?suffix=FAIL\';this.onerror=null;" height="100" width="'+width+'" style="border:1px dashed #E6E6E6;max-width:100%;object-fit: cover;">';
						html += '<div class="images-delete" data-maxcount="'+imgcount+'" data-multiple="'+multiple+'" data-src="'+imgstr+'" style="position:relative;top:-111px;left:'+pleft+';cursor:pointer;">';
						html += '<img src="/assets/img/attach_delete.png" style="height:16px;width:16px;"></div></div>';
					});

					if(multiple == 0){
						$(_this).hide();
					}
					if(multiple == 1 && defs.length > 0){
						arrs = arrs.concat(defs);
					}
					$(_this).before(html);
					$("input[name='"+inputval+"']").attr("value",arrs.join(","));
					top.layer.close(index);
				}
			});
		});
		
		//图片选择器 - 删除
		$(document).on("click",".images-delete",function(){
			var imgcount = $(this).data('maxcount');
			var multiple = $(this).data('multiple');
			//多图片处理
			if(multiple == "1"){
				var imagesrc = $(this).data("src");
				var inputval = $(this).parent().parent().find("input[type='hidden']").val();
				inputval = inputval.replace(","+imagesrc,"");
				inputval = inputval.replace(imagesrc+",","");
				inputval = inputval.replace(imagesrc,"");
				inputval = inputval.replace(",,","");
				$(this).parent().parent().find("input[type='hidden']").attr("value",inputval);
				var inputcount = inputval.split(",").length;
				if(typeof(imgcount) == "undefined"){
					imgcount = 0;
				}else{
					imgcount = parseInt(imgcount);
				}
				if(imgcount > inputcount){
					$(this).parent().parent().find(".select-upload").show();
				}
				$(this).parent().remove();
			}
			
			//单图片处理
			if(multiple == "0"){
				$(this).parent().parent().find("input[type='hidden']").attr("value","");
				$(this).parent().parent().find(".select-upload").show();
				$(this).parent().remove();
			}
		});
		
		//回显图片 - 轮询
		$(".select-upload").each(function(){
			var _this	 = this;
			var state    = false;
			var shape    = $(_this).attr("data-shape"); //图片形状 
			var inputval = $(_this).attr("data-input"); //输入值
			var imgcount = $(_this).attr("data-maxcount"); //图片最大选择数
			var multiple = ($(_this).attr("data-multiple") == "true") ? 1 : 0;
			let domain = window.location.protocol + '//'+ document.location.host + Config.moduleurl;
			var cdnurl   =  Config.upload.cdnurl;
			var getvalue = "";
			var width    = '100';
			var pleft    = '91px';
			
			if(typeof(shape) !== "undefined" && shape == "oblong"){
				width = '150';
				pleft = '141px'
			}
			if(typeof(inputval) !== "undefined"){
				state = true;
			}
			
			//图片选择最大数处理
			if(typeof(imgcount) == "undefined"){
				imgcount = 0; //不限制
			}else{
				imgcount = parseInt(imgcount);
			}
			
			if(state){
				getvalue = $(_this).parent().find("input[name='"+inputval+"']").val(); //取值
				if(typeof(getvalue) == "undefined"){
					state = false;
				}
			}
			if(state && getvalue.length > 1){
				var html = '';
				var arrs = getvalue.split(",");
				for (let imgstr in arrs) {
					html += '<div class="select-images" style="float:left;margin-right:4%;">';
					html += '<img src="'+cdnurl+arrs[imgstr]+'" data-src="'+arrs[imgstr]+'" onerror="this.src=\''+domain+'/ajax/icon?suffix=FAIL\';this.onerror=null;" height="100" width="'+width+'" style="border:1px dashed #E6E6E6;max-width:100%;object-fit: cover;">';
					html += '<div class="images-delete" data-maxcount="'+imgcount+'" data-multiple="'+multiple+'" data-src="'+arrs[imgstr]+'" style="position:relative;top:-111px;left:'+pleft+';cursor:pointer;">';
					html += '<img src="/assets/img/attach_delete.png" style="height:16px;width:16px;"></div></div>';
				}
				if(multiple == 0){
					$(_this).hide();
				}
				if(multiple == 1 && imgcount == arrs.length){
					$(_this).hide();
				}
				$(_this).before(html);
			}
		});
	}
});
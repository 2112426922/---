define([], function () {
    require(['../addons/bootstrapcontextmenu/js/bootstrap-contextmenu'], function (undefined) {
    if (Config.controllername == 'index' && Config.actionname == 'index') {
        $("body").append(
            '<div id="context-menu">' +
            '<ul class="dropdown-menu" role="menu">' +
            '<li><a tabindex="-1" data-operate="refresh"><i class="fa fa-refresh fa-fw"></i>刷新</a></li>' +
            '<li><a tabindex="-1" data-operate="refreshTable"><i class="fa fa-table fa-fw"></i>刷新表格</a></li>' +
            '<li><a tabindex="-1" data-operate="close"><i class="fa fa-close fa-fw"></i>关闭</a></li>' +
            '<li><a tabindex="-1" data-operate="closeOther"><i class="fa fa-window-close-o fa-fw"></i>关闭其他</a></li>' +
            '<li class="divider"></li>' +
            '<li><a tabindex="-1" data-operate="closeAll"><i class="fa fa-power-off fa-fw"></i>关闭全部</a></li>' +
            '</ul>' +
            '</div>');

        $(".nav-addtabs").contextmenu({
            target: "#context-menu",
            scopes: 'li[role=presentation]',
            onItem: function (e, event) {
                var $element = $(event.target);
                var tab_id = e.attr('id');
                var id = tab_id.substr('tab_'.length);
                var con_id = 'con_' + id;
                switch ($element.data('operate')) {
                    case 'refresh':
                        $("#" + con_id + " iframe").attr('src', function (i, val) {
                            return val;
                        });
                        break;
                    case 'refreshTable':
                        try {
                            if ($("#" + con_id + " iframe").contents().find(".btn-refresh").size() > 0) {
                                $("#" + con_id + " iframe")[0].contentWindow.$(".btn-refresh").trigger("click");
                            }
                        } catch (e) {

                        }
                        break;
                    case 'close':
                        if (e.find(".close-tab").length > 0) {
                            e.find(".close-tab").click();
                        }
                        break;
                    case 'closeOther':
                        e.parent().find("li[role='presentation']").each(function () {
                            if ($(this).attr('id') == tab_id) {
                                return;
                            }
                            if ($(this).find(".close-tab").length > 0) {
                                $(this).find(".close-tab").click();
                            }
                        });
                        break;
                    case 'closeAll':
                        e.parent().find("li[role='presentation']").each(function () {
                            if ($(this).find(".close-tab").length > 0) {
                                $(this).find(".close-tab").click();
                            }
                        });
                        break;
                    default:
                        break;
                }
            }
        });
    }
    $(document).on('click', function () { // iframe内点击 隐藏菜单
        try {
            top.window.$(".nav-addtabs").contextmenu("closemenu");
        } catch (e) {
        }
    });

});
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
require.config({
    paths: {
        'clicaptcha': '../addons/clicaptcha/js/clicaptcha'
    },
    shim: {
        'clicaptcha': {
            deps: [
                'jquery',
                'css!../addons/clicaptcha/css/clicaptcha.css'
            ],
            exports: '$.fn.clicaptcha'
        }
    }
});

require(['form'], function (Form) {
    window.clicaptcha = function (captcha) {
        require(['clicaptcha'], function (undefined) {
            captcha = captcha ? captcha : $("input[name=captcha]");
            if (captcha.length > 0) {
                var form = captcha.closest("form");
                var parentDom = captcha.parent();
                // 非文本验证码
                if ($("a[data-event][data-url]", parentDom).length > 0) {
                    return;
                }
                if (captcha.parentsUntil(form, "div.form-group").length > 0) {
                    captcha.parentsUntil(form, "div.form-group").addClass("hidden");
                } else if (parentDom.is("div.input-group")) {
                    parentDom.addClass("hidden");
                }
                captcha.attr("data-rule", "required");
                // 验证失败时进行操作
                captcha.on('invalid.field', function (e, result, me) {
                    //必须删除errors对象中的数据，否则会出现Layer的Tip
                    delete me.errors['captcha'];
                    captcha.clicaptcha({
                        src: '/addons/clicaptcha/index/start',
                        success_tip: '验证成功！',
                        error_tip: '未点中正确区域，请重试！',
                        callback: function (captchainfo) {
                            form.trigger("submit");
                            return false;
                        }
                    });
                });
                // 监听表单错误事件
                form.on("error.form", function (e, data) {
                    captcha.val('');
                });
            }
        });
    };
    // clicaptcha($("input[name=captcha]"));

    if (typeof Frontend !== 'undefined') {
        Frontend.api.preparecaptcha = function (btn, type, data) {
            require(['form'], function (Form) {
                $("#clicaptchacontainer").remove();
                $("<div />").attr("id", "clicaptchacontainer").addClass("hidden").html(Template("captchatpl", {})).appendTo("body");
                var form = $("#clicaptchacontainer form");
                form.data("validator-options", {
                    valid: function (ret) {
                        data.captcha = $("input[name=captcha]", form).val();
                        Frontend.api.sendcaptcha(btn, type, data, function (data, ret) {
                            console.log("ok");
                        });
                        return true;
                    }
                })
                Form.api.bindevent(form);
            });
        };
    }

    var _bindevent = Form.events.bindevent;
    Form.events.bindevent = function (form) {
        _bindevent.apply(this, [form]);
        var captchaObj = $("input[name=captcha]", form);
        if (captchaObj.length > 0) {
            clicaptcha(captchaObj);
            if ($(form).attr("name") === 'captcha-form') {
                setTimeout(function () {
                    captchaObj.trigger("invalid.field", [{key: 'captcha'}, {errors: {}}]);
                }, 100);
            }
        }
    }
});

require(['form', 'upload'], function (Form, Upload) {
    var _bindevent = Form.events.bindevent;
    Form.events.bindevent = function (form) {
        _bindevent.apply(this, [form]);

        if ($("#croppertpl").length == 0) {
            var allowAttr = [
                'aspectRatio', 'autoCropArea', 'cropBoxMovable', 'cropBoxResizable', 'minCropBoxWidth', 'minCropBoxHeight', 'minContainerWidth', 'minContainerHeight',
                'minCanvasHeight', 'minCanvasWidth', 'croppedWidth', 'croppedHeight', 'croppedMinWidth', 'croppedMinHeight', 'croppedMaxWidth', 'croppedMaxHeight', 'fillColor',
                'containerMinHeight', 'containerMaxHeight', 'customWidthHeight', 'customAspectRatio'
            ];
            String.prototype.toLineCase = function () {
                return this.replace(/[A-Z]/g, function (match) {
                    return "-" + match.toLowerCase();
                });
            };

            var btnAttr = [];
            $.each(allowAttr, function (i, j) {
                btnAttr.push('data-' + j.toLineCase() + '="<%=data.' + j + '%>"');
            });

            var btn = '<button class="btn btn-success btn-cropper btn-xs" data-input-id="<%=data.inputId%>" ' + btnAttr.join(" ") + ' style="position:absolute;top:10px;right:15px;">裁剪</button>';

            var insertBtn = function () {
                return arguments[0].replace(arguments[2], btn + arguments[2]);
            };
            $("<script type='text/html' id='croppertpl'>" + Upload.config.previewtpl.replace(/<li(.*?)>(.*?)<\/li>/, insertBtn) + "</script>").appendTo("body");
        }

        $(".plupload[data-preview-id],.faupload[data-preview-id]").each(function () {
            var preview_id = $(this).data("preview-id");
            var previewObj = $("#" + preview_id);
            var tpl = previewObj.length > 0 ? previewObj.data("template") : '';
            if (!tpl) {
                if (!$(this).hasClass("cropper")) {
                    $(this).addClass("cropper");
                }
                previewObj.data("template", "croppertpl");
            }
        });

        //图片裁剪
        $(document).off('click', '.btn-cropper').on('click', '.btn-cropper', function () {
            var image = $(this).closest("li").find('.thumbnail').data('url');
            var input = $("#" + $(this).data("input-id"));
            var url = image;
            var data = $(this).data();
            var params = [];
            $.each(allowAttr, function (i, j) {
                if (typeof data[j] !== 'undefined' && data[j] !== '') {
                    params.push(j + '=' + data[j]);
                }
            });
            try {
                var parentWin = (parent ? parent : window);
                parentWin.Fast.api.open('/addons/cropper/index/cropper?url=' + image + (params.length > 0 ? '&' + params.join('&') : ''), '裁剪', {
                    callback: function (data) {
                        if (typeof data !== 'undefined') {
                            var arr = data.dataURI.split(','), mime = arr[0].match(/:(.*?);/)[1],
                                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                            while (n--) {
                                u8arr[n] = bstr.charCodeAt(n);
                            }
                            var urlArr = url.split('.');
                            var suffix = 'png';
                            url = urlArr.join('');
                            var filename = url.substr(url.lastIndexOf('/') + 1);
                            var exp = new RegExp("\\." + suffix + "$", "i");
                            filename = exp.test(filename) ? filename : filename + "." + suffix;
                            var file = new File([u8arr], filename, {type: mime});
                            Upload.api.send(file, function (data) {
                                input.val(input.val().replace(image, data.url)).trigger("change");
                            }, function (data) {
                            });
                        }
                    },
                    area: [Math.min(parentWin.$(parentWin.window).width(), Config.cropper.dialogWidth) + "px", Math.min(parentWin.$(parentWin.window).height(), Config.cropper.dialogHeight) + "px"],
                });
            } catch (e) {
                console.error(e);
            }
            return false;
        });
    }
});

//判断系统深色模式变化，修改切换按钮
var matchMedia = window.matchMedia(('(prefers-color-scheme: dark)'));
matchMedia.addEventListener('change', function () {
    var mode = this.matches ? 'dark' : 'light';
    //只有当cookie中无手动定义值时才进行操作
    if (document.cookie.indexOf("thememode=") === -1 && Config.darktheme.mode === 'auto') {
        $("body").toggleClass("darktheme", mode === "dark");
    }
});

if (typeof Config.darktheme !== 'undefined' && Config.darktheme.switchbtn) {

    // 切换模式
    var switchMode = function (mode) {
        // 获取当前深色模式
        if (mode === 'auto') {
            var isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            mode = isDarkMode ? 'dark' : 'light';
        }

        if (mode === 'auto') {
        } else if (mode === 'dark') {
            $("body").addClass("darktheme");
            $(".darktheme-link").removeAttr("media");
        } else {
            $("body").removeClass("darktheme");
            $(".darktheme-link").attr("media", "(prefers-color-scheme: dark)");
        }
    };

    // 创建Cookie
    var createCookie = function (name, value) {
        var date = new Date();
        date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
        var url = Config.moduleurl.replace(location.origin, "");
        var path = url ? url.substring(url.lastIndexOf("/")) : "/";
        document.cookie = encodeURIComponent(Config.cookie.prefix + name) + "=" + encodeURIComponent(value) + "; path=" + path + "; expires=" + date.toGMTString();
    };

    if (Config.controllername === 'index' && Config.actionname === 'index') {
        var mode = Config.darktheme.mode;
        if (mode === 'auto') {
            var isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            mode = isDarkMode ? 'dark' : 'light';
        }
        var html = '<li class="theme-li">' +
            '<button type="button" title="切换' + (mode === 'dark' ? '浅色' : '深色') + '模式" data-mode="' + (mode === 'dark' ? 'light' : 'dark') + '" class="theme-toggle">' +
            '<svg class="sun-and-moon" aria-hidden="true" width="24" height="24" viewBox="0 0 24 24">\n' +
            '      <circle class="sun" cx="12" cy="12" r="6" mask="url(#moon-mask)" fill="currentColor" />\n' +
            '      <g class="sun-beams" stroke="currentColor">\n' +
            '        <line x1="12" y1="1" x2="12" y2="3" />\n' +
            '        <line x1="12" y1="21" x2="12" y2="23" />\n' +
            '        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />\n' +
            '        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />\n' +
            '        <line x1="1" y1="12" x2="3" y2="12" />\n' +
            '        <line x1="21" y1="12" x2="23" y2="12" />\n' +
            '        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />\n' +
            '        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />\n' +
            '      </g>\n' +
            '      <mask class="moon" id="moon-mask">\n' +
            '        <rect x="0" y="0" width="100%" height="100%" fill="white" />\n' +
            '        <circle cx="24" cy="10" r="6" fill="black" />\n' +
            '      </mask>\n' +
            '    </svg>' +
            '</button>' +
            '</li>';
        $(html).prependTo("#firstnav > div > ul");

        //点击切换按钮
        $(document).on("click", ".theme-toggle", function () {
            var mode = $(this).attr("data-mode");
            switchMode(mode);
            createCookie("thememode", mode);
            $("iframe").each(function () {
                try {
                    $(this)[0].contentWindow.$("body").trigger("swithmode", [mode]);
                } catch (e) {

                }
            });
            $(this).attr("data-mode", mode === 'dark' ? 'light' : 'dark').attr("title", '切换' + (mode === 'dark' ? '浅色' : '深色') + '模式');
        });

        //判断系统深色模式变化，修改切换按钮
        var matchMedia = window.matchMedia(('(prefers-color-scheme: dark)'));
        matchMedia.addEventListener('change', function () {
            var mode = this.matches ? 'dark' : 'light';
            //只有当cookie中无手动定义值时才切换
            if (document.cookie.indexOf("thememode=") === -1 && Config.darktheme.mode === 'auto') {
                $(".theme-toggle").attr("data-mode", mode === 'dark' ? 'light' : 'dark').attr("title", '切换' + (mode === 'dark' ? '浅色' : '深色') + '模式');
            }
        });
    } else {
        //添加事件
        $("body").on("swithmode", function (e, mode) {
            switchMode(mode);
            $("iframe").each(function () {
                try {
                    $(this)[0].contentWindow.$("body").trigger("swithmode", [mode]);
                } catch (e) {

                }
            });
        });
    }
}
require.config({
    paths: {
        'idletimer': '../addons/lockscreen/idle-timer.min',
    }
});
require(['idletimer'], function (idleTimer) {
    if (Config.lockscreen.module == 'admin' && Config.lockscreen.controller.toLowerCase() == 'index' && Config.lockscreen.action.toLowerCase() == 'index') {
        //浮动按钮
        var _html = '<li class="hidden-xs"><a href="'+Config.lockscreen.baseUrl+'"><i class="fa fa-lock"></i> 锁定</a></li>';
        $(".navbar-custom-menu>ul","#firstnav").prepend(_html);
    }
    var Lockscreen = {
        start: function () {
            if (Config.lockscreen.status === '1') {
                if ($.inArray(Config.modulename, ['admin']) !== -1) {
                    if ($.inArray(Config.controllername, ['lockscreen']) === -1) {
                        if ($.inArray(Config.actionname, ['login']) === -1) {
                            if (typeof (localStorage) !== "undefined") {
                                $(document).ready(function () {
                                    var opts = {
                                        timeout: Config.lockscreen.overtime * 1000 * 60,
                                        timerSyncId: 'lockscreen',
                                        tId: 1986,
                                        events: 'mousemove keydown wheel DOMMouseScroll mousewheel mousedown touchstart touchmove MSPointerDown MSPointerMove'
                                    };
                                    $(document).idleTimer(opts);
                                    if (Config.lockscreen.debug === '0' && Config.lockscreen.password === '1') {
                                        Lockscreen.api.arrestDebuger();
                                    }

                                });
                                $(document).on("idle.idleTimer", function (event, elem, obj) {
                                    if (!localStorage.getItem("lockIdle")) {
                                        Lockscreen.lock();
                                    }
                                });
                                $(document).on("active.idleTimer", function (event, elem, obj, triggerevent) {
                                    if (!localStorage.getItem("lockActive")) {
                                        Lockscreen.unlock();
                                    }
                                });
                            }
                        }
                    }
                }
            }
        },
        lock: function () {
            $.ajax({
                url: 'lockscreen/setLock',
                method: 'POST'
            });
            localStorage.setItem('lockIdle', true);
            localStorage.removeItem('lockActive');
            if (Config.lockscreen.password === '0') {
                Lockscreen.tips();
            } else {
                Lockscreen.prompt();
            }
        },
        unlock: function () {
            localStorage.setItem('lockActive', true);
            localStorage.removeItem('lockIdle');
            if (Config.lockscreen.password === "0") {
                $.ajax({
                    url: 'lockscreen/setUnlock',
                    method: 'POST'
                });
                parent.Layer.closeAll();
            }
        },
        tips: function () {
            parent.Layer.open({
                type: 1,
                title: false,
                id: 2019,
                area: '387px', //宽高
                closeBtn: 0, //不显示关闭按钮
                shade: 1,
                content: '<div class="small-box bg-blue" style="margin-bottom:0px">' +
                '<div class="inner"><h3>已锁定</h3><p>' + Config.site.name + '</p></div>' +
                '<div class="icon"><i class="fa fa-lock"></i></div>' +
                '<a href="#" class="small-box-footer"> 移动鼠标或按键解锁 <i class="fa fa-arrow-circle-right"></i></a></div>'
            });
        },
        prompt: function () {
            parent.Layer.prompt({
                title: '请输入密码解锁',
                formType: 1,
                shade: 1,
                anim: 5,
                closeBtn: 0,
                id: 2019,
                btn2: function (index) {
                    return false;
                },
                success: function (layero) { // 增加回车确认事件
                    layero.find("input[type='password']").on('keydown', function (event) {
                        if (event.keyCode === 13) {
                            Lockscreen.api.unlockPassword(layero.find("input[type='password']").val());
                        }
                    })
                },
                end: function () {
                    $.ajax({
                        url: 'lockscreen/getLock',
                        method: 'GET',
                        success: function (json) {
                            if (json.status) {
                                Lockscreen.prompt();
                            }
                        }
                    });
                }
            }, function (password, index) {
                Lockscreen.api.unlockPassword(password);
            });
        },
        api: {
            arrestDebuger: function () {
                var element = new Image();
                Object.defineProperty(element, 'id', {
                    get: function () {
                        window.location.href = "/admin/lockscreen/lock?console=&url=" + Config.controllername + "%2F" + Config.actionname;
                    }
                });
                console.log(element);
            },
            unlockPassword: function (password) {
                $.ajax({
                    url: 'lockscreen/unlock',
                    data: {
                        password: password
                    },
                    method: 'POST',
                    success: function (json) {
                        if (json.code) {
                            parent.Layer.closeAll();
                        } else {
                            parent.Layer.msg(json.msg);
                        }
                    }
                });
            }
        }
    };
    Lockscreen.start();
});
require.config({
    paths: {
        'nkeditor': '../addons/nkeditor/js/customplugin',
        'nkeditor-core': '../addons/nkeditor/nkeditor',
        'nkeditor-lang': '../addons/nkeditor/lang/zh-CN',
    },
    shim: {
        'nkeditor': {
            deps: [
                'nkeditor-core',
                'nkeditor-lang'
            ]
        },
        'nkeditor-core': {
            deps: [
                'css!../addons/nkeditor/themes/black/editor.min.css',
                'css!../addons/nkeditor/css/common.css'
            ],
            exports: 'window.KindEditor'
        },
        'nkeditor-lang': {
            deps: [
                'nkeditor-core'
            ]
        }
    }
});
require(['form'], function (Form) {
    var _bindevent = Form.events.bindevent;
    Form.events.bindevent = function (form) {
        _bindevent.apply(this, [form]);
        if ($(Config.nkeditor.classname || '.editor', form).length > 0) {
            require(['nkeditor', 'upload'], function (Nkeditor, Upload) {
                var getFileFromBase64, uploadFiles;
                uploadFiles = async function (files) {
                    var self = this;
                    for (var i = 0; i < files.length; i++) {
                        try {
                            await new Promise((resolve) => {
                                var url, html, file;
                                file = files[i];
                                Upload.api.send(file, function (data) {
                                    url = Config.nkeditor.fullmode ? Fast.api.cdnurl(data.url, true) : Fast.api.cdnurl(data.url);
                                    if (file.type.indexOf("image") !== -1) {
                                        self.exec("insertimage", url);
                                    } else {
                                        html = '<a class="ke-insertfile" href="' + url + '" data-ke-src="' + url + '" target="_blank">' + (file.name || url) + '</a>';
                                        self.exec("inserthtml", html);
                                    }
                                    resolve();
                                }, function () {
                                    resolve();
                                });
                            });
                        } catch (e) {

                        }
                    }
                };
                getFileFromBase64 = function (data, url) {
                    var arr = data.split(','), mime = arr[0].match(/:(.*?);/)[1],
                        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                    while (n--) {
                        u8arr[n] = bstr.charCodeAt(n);
                    }
                    var filename, suffix;
                    if (typeof url != 'undefined') {
                        var urlArr = url.split('.');
                        filename = url.substr(url.lastIndexOf('/') + 1);
                        suffix = urlArr.pop();
                    } else {
                        filename = Math.random().toString(36).substring(5, 15);
                    }
                    if (!suffix) {
                        suffix = data.substring("data:image/".length, data.indexOf(";base64"));
                    }

                    var exp = new RegExp("\\." + suffix + "$", "i");
                    filename = exp.test(filename) ? filename : filename + "." + suffix;
                    var file = new File([u8arr], filename, {type: mime});
                    return file;
                };

                $(Config.nkeditor.classname || '.editor', form).each(function () {
                    var that = this;
                    var options = $(this).data("nkeditor-options");
                    var editor = Nkeditor.create(that, $.extend({}, {
                        width: '100%',
                        filterMode: false,
                        wellFormatMode: false,
                        allowMediaUpload: true, //是否允许媒体上传
                        allowFileManager: true,
                        allowImageUpload: true,
                        baiduMapKey: Config.nkeditor.baidumapkey || '',
                        baiduMapCenter: Config.nkeditor.baidumapcenter || '',
                        fontSizeTable: ['9px', '10px', '12px', '14px', '16px', '18px', '21px', '24px', '32px'],
                        formulaPreviewUrl: typeof Config.nkeditor != 'undefined' && Config.nkeditor.formulapreviewurl ? Config.nkeditor.formulapreviewurl : "", //数学公式的预览地址
                        cssPath: Config.site.cdnurl + '/assets/addons/nkeditor/plugins/code/prism.css',
                        cssData: "body {font-size: 13px}",
                        fillDescAfterUploadImage: false, //是否在上传后继续添加描述信息
                        themeType: typeof Config.nkeditor != 'undefined' ? Config.nkeditor.theme : 'black', //编辑器皮肤,这个值从后台获取
                        fileManagerJson: Fast.api.fixurl("/addons/nkeditor/index/attachment/module/" + Config.modulename),
                        items: [
                            'source', 'undo', 'redo', 'preview', 'print', 'template', 'code', 'quote', 'cut', 'copy', 'paste',
                            'plainpaste', 'justifyleft', 'justifycenter', 'justifyright',
                            'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                            'superscript', 'clearhtml', 'quickformat', 'selectall',
                            'formatblock', 'fontname', 'fontsize', 'forecolor', 'hilitecolor', 'bold',
                            'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', 'image', 'multiimage', 'graft',
                            'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
                            'anchor', 'link', 'unlink', 'remoteimage', 'search', 'math', 'about', 'fullscreen'
                        ],
                        afterCreate: function () {
                            var self = this;
                            //Ctrl+回车提交
                            Nkeditor.ctrl(document, 13, function () {
                                self.sync();
                                $(that).closest("form").submit();
                            });
                            Nkeditor.ctrl(self.edit.doc, 13, function () {
                                self.sync();
                                $(that).closest("form").submit();
                            });
                            //粘贴上传
                            $("body", self.edit.doc).bind('paste', function (event) {
                                var originalEvent;
                                originalEvent = event.originalEvent;
                                if (originalEvent.clipboardData && originalEvent.clipboardData.files.length > 0) {
                                    uploadFiles.call(self, originalEvent.clipboardData.files);
                                    return false;
                                }
                            });
                            //拖拽上传
                            $("body", self.edit.doc).bind('drop', function (event) {
                                var originalEvent;
                                originalEvent = event.originalEvent;
                                if (originalEvent.dataTransfer && originalEvent.dataTransfer.files.length > 0) {
                                    uploadFiles.call(self, originalEvent.dataTransfer.files);
                                    return false;
                                }
                            });
                        },
                        afterChange: function () {
                            $(this.srcElement[0]).trigger("change");
                        },
                        //自定义处理
                        beforeUpload: function (callback, file) {
                            var file = file ? file : $("input.ke-upload-file", this.form).prop('files')[0];
                            Upload.api.send(file, function (data) {
                                var data = {code: '000', data: {url: Config.nkeditor.fullmode ? Fast.api.cdnurl(data.url, true) : Fast.api.cdnurl(data.url)}, title: '', width: '', height: '', border: '', align: ''};
                                callback(data);
                            });
                        },
                        //错误处理 handler
                        errorMsgHandler: function (message, type) {
                            try {
                                Fast.api.msg(message);
                                console.log(message, type);
                            } catch (Error) {
                                alert(message);
                            }
                        },
                        uploadFiles: uploadFiles
                    }, options || {}));
                    $(this).data("nkeditor", editor);
                });
            });
        }
    }
});

require.config({
    paths: {
        'qrcode': '../addons/notice/js/qrcode',
        'HackTimer': '../addons/notice/js/HackTimer.min',
    },
    shim: {
    }
});

function ajaxInit() {
    if (Config.modulename == 'admin') {
        if (!(Config.controllername == 'index' && Config.actionname == 'index' && Config.notice.admin_real == 1)) {
            return false;
        }
    } else if (Config.modulename == 'index'){
        if (Config.notice.user_real != 1) {
            return false;
        }
        if (!indexUrlCheck()) {
            return false;
        }
    } else {
        return false;
    }
    console.log('ajax_init');

    require(['HackTimer'], function (HackTimer) {
        var url = '';
        if (Config.modulename == 'admin') {
            url = 'notice/admin/statistical';
            noticePanel.insertHtml();
        }
        if (Config.modulename == 'index') {
            url = '/addons/notice/api/statistical';
        }

        // 获取新消息并提示
        function notice() {
            Fast.api.ajax({
                url: url,
                loading: false
            }, function (data, res) {
                if (data.new) {
                    Toastr.info(data.new.content);
                }
                if (Config.modulename == 'admin') {
                    Backend.api.sidebar({
                        'notice/admin': data.num,
                    });
                    noticePanel.render(data.notice_data, data.wait_data);
                }
                setTimeout(function () {
                    notice();
                }, 5000);
                return false;
            }, function () {
                return false;
            });
        };

        notice();
    });
};

function wsInit() {
    if (Config.modulename == 'admin') {
        if (!(Config.controllername == 'index' && Config.actionname == 'index' && Config.notice.admin_real == 2)) {
            return false;
        }
    } else if (Config.modulename == 'index'){
        if (!indexUrlCheck()) {
            return false;
        }
        if (Config.notice.user_real != 2) {
            return false;
        }
    } else {
        return false;
    }
    console.log('ws_init');

    let NhWs = {
        ws: null,
        timer: null,
        bindurl: '',
        url: '',
        connect: function () {
            var ws = new WebSocket(this.url);
            this.ws = ws;

            ws.onmessage = this.onmessage;
            ws.onclose = this.onclose;
            ws.onerror = this.onerror;
            ws.onopen = this.onopen;
        },
        onmessage: function (e) {
            // json数据转换成js对象
            var data = e.data;
            try {
                JSON.parse(data);
                data = JSON.parse(data) ? JSON.parse(data) : data;
            } catch {
                console.log('ws接收到非对象数据', data);
                return true;
            }
            console.log('ws接收到数据', data, e.data);

            var type = data.type || '';
            var resdata = data.data ? data.data : {};
            switch(type){
                case 'init':
                    $.ajax(NhWs.bindurl, {
                        data: {
                            client_id: data.client_id
                        },
                        method: 'post'
                    })
                    break;
                case "new_notice":
                    if (Config.modulename == 'admin') {
                        Backend.api.sidebar({
                            'notice/admin': resdata.num,
                        });
                    }
                    Toastr.info(resdata.msg);

                    // 发送ajax到后台告诉已经看过这条消息
                    Fast.api.ajax({
                        url: '/addons/notice/api/cache',
                        data: {
                            time: resdata.time,
                            module: Config.modulename
                        },
                        method: 'post'
                    }, function () {
                        return false;
                    });
                    break;
                case "notice_panel":
                    noticePanel.render(resdata.notice_data, resdata.wait_data);
                    break;
            }
        },
        onclose: function () {
            console.log('连接已断开，尝试自动连接');
            setTimeout(function () {
                NhWs.connect();
            }, 5000);
        },

        onopen: function () {
            this.timer = setInterval(function () {
                NhWs.send({"type":"ping"});
            }, 20000);
        },
        onerror: function () {
            console.log('ws连接失败');
            Toastr.error('ws连接失败');
        },

        // 发送数据
        send: function (data) {
            if (typeof data == "object") {
                data = JSON.stringify(data);
            }
            this.ws.send(data);
        },
    };

    if (Config.modulename == 'admin') {
        noticePanel.insertHtml();
        NhWs.bindurl = Fast.api.fixurl('/addons/notice/ws/bindAdmin');
        // ajax请求获取消息数量等
        Fast.api.ajax({
            url: 'notice/admin/statistical',
            loading: false,
            method: 'post',
        }, function (data, res) {
            if (data.new) {
                Toastr.info(data.new.content);
            }
            Backend.api.sidebar({
                'notice/admin': data.num,
            });
            noticePanel.render(data.notice_data, data.wait_data);
            return false;
        }, function () {
            return false;
        });

    }

    if (Config.modulename == 'index') {
        NhWs.bindurl = Fast.api.fixurl('/addons/notice/ws/bind');
        // ajax请求最新获取消息数量等
        Fast.api.ajax({
            url: '/addons/notice/api/statistical',
            loading: false,
            method: 'post',
        }, function (data, res) {
            if (data.new) {
                Toastr.info(data.new.content);
            }
            return false;
        }, function () {
            return false;
        });
    }
    NhWs.url = Config.notice.wsurl;

    require(['HackTimer'], function (HackTimer) {
        NhWs.connect();
    });
};

function indexUrlCheck() {
    if (Config.modulename == 'index') {
        var url = Config.controllername+'/'+Config.actionname;
        if (Config.notice.user_real_url.indexOf('*') === -1) {
            if (Config.notice.user_real_url.indexOf(url) === -1) {
                return false;
            }
        }
    }

    return true;
};

let noticePanel = {
    insertHtml: function () {
        if (!Config.notice.admin_check) {
            return false;
        }

        var html = '   <style>\n' +
            '                    #addon-notice:hover {\n' +
            '                        background: #2A404A;\n' +
            '                    }\n' +
            '\n' +
            '                    #addon-notice:hover>a{\n' +
            '                        color: #ffffff;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-box {\n' +
            '                        padding: 0 !important;\n' +
            '                        width: 320px;\n' +
            '                        border: none !important;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-box li {\n' +
            '                        width: 160px;\n' +
            '                        height: 42px;\n' +
            '                        border: none;\n' +
            '                        border: none !important;\n' +
            '                        text-align: center;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-list {\n' +
            '                        padding: 10px !important;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-list ul {\n' +
            '                        padding: 10px;\n' +
            '                        padding-top: 0px;\n' +
            '                        padding-bottom: 0px;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-list .line {\n' +
            '                        border-bottom: 1px solid #3B525D;\n' +
            '                        margin: 0 -10px;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-list li div{\n' +
            '                        display: inline-block;\n' +
            '                        color: white;\n' +
            '                        line-height: 40px;\n' +
            '                        height: 40px;\n' +
            '                        vertical-align: top;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-list li>a{\n' +
            '                        display: inline-block;\n' +
            '                        width: 100%;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-list li .icon{\n' +
            '                        width: 20px;\n' +
            '                        margin-top: -2px;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-list li .text{\n' +
            '                        width: 160px;\n' +
            '                        overflow:hidden;\n' +
            '                        text-overflow:ellipsis;\n' +
            '                        white-space:nowrap;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-list li .time{\n' +
            '                        width: 80px;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-button {\n' +
            '                        display: flex;\n' +
            '                        flex-direction: row;\n' +
            '                        color: #839DA8;\n' +
            '                        justify-content: space-between;\n' +
            '                        padding-top: 15px;\n' +
            '                        padding-bottom: 0px;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-button a{\n' +
            '                        color: #839DA8;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-box ul li {\n' +
            '                        background: #222D32;\n' +
            '                    }\n' +
            '\n' +
            '                    .common-notice-box ul li.active {\n' +
            '                        background: transparent;\n' +
            '                    }\n' +
            '\n' +
            '                </style>\n' +
            '                <li class="hidden-xs" id="addon-notice"  onmouseleave="$(\'#noticelist\').stop().fadeOut(300);" onmouseenter="$(\'#noticelist\').stop().fadeIn(300);">\n' +
            '                    <a href="#" data-title="消息通知"><i class="fa fa-bell" style="font-size:14px;"></i> 消息\n' +
            '                        <div id="notice-all-num" style="width: 16px;height: 16px;border-radius:50%;background-color: red;color: #FFFFFF;position: absolute; right: 0; top: 10px; line-height: 16px; display: none; text-align: center;"></div>\n' +
            '                    </a>\n' +
            '\n' +
            '                    <div style="position: absolute;width: 320px;opacity: 1;background: #2A404A; overflow: hidden;display:none; left: -160px;" id="noticelist">\n' +
            '                        <div class="panel-heading common-notice-box">\n' +
            '                            <ul class="nav nav-tabs" style="border: none; padding: 0;">\n' +
            '                                <li class="active"><a href="#notice-one" data-toggle="tab" style="color: #FFFFFF; border: none;">待办\n' +
            '                                    <div class="wait-num" style="width: 16px;height: 16px;border-radius:50%;background-color: red;color: #FFFFFF;position: absolute;margin: -21px 44px -43px 86px; line-height: 16px; display: none;">-</div>\n' +
            '                                </a>\n' +
            '\n' +
            '                                </li>\n' +
            '                                <li><a href="#notice-two" data-toggle="tab" style="color: #FFFFFF; border: none;">通知\n' +
            '                                    <div class="notice-num" style="width: 16px;height: 16px;border-radius:50%;background-color: red;color: #FFFFFF;position: absolute;margin: -21px 44px -43px 86px; line-height: 16px; display: none">-</div>\n' +
            '                                </a></li>\n' +
            '                            </ul>\n' +
            '                        </div>\n' +
            '\n' +
            '                        <div id="myTabContent" class="tab-content">\n' +
            '                            <div class="tab-pane fade active in common-notice-list" id="notice-one">\n' +
            '                                <ul>\n' +
            '                                    查询中...\n' +
            '                                </ul>\n' +
            '                            </div>\n' +
            '\n' +
            '                            <div class="tab-pane fade common-notice-list" id="notice-two">\n' +
            '                                <ul>\n' +
            '                                    查询中...\n' +
            '                                </ul>\n' +
            '\n' +
            '                                <div class="line"></div>\n' +
            '                                <div class="common-notice-button">\n' +
            '                                    <a href="javascript:;" class="hidden"  data-url="{:url(\'notice/admin/mark\')}">标记当前已读</a>\n' +
            '                                    <a href="notice/admin" data-title="我的消息" class="btn-addtabs" >查看全部</a>\n' +
            '                                </div>\n' +
            '                            </div>\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                </li>';
        $('.user-menu').before(html);
    },

    render: function (noticeData, waitData) {
        // 后台右上角通知/待办等
        // var noticeData = {
        //     num: 5,
        //     list: [
        //         {
        //             'title': '待办01',
        //             'time':'xxx',
        //             'url':'',
        //             'class': '',
        //         },
        //         {
        //             'title': '待办02',
        //             'time':'xxx',
        //             'url':'',
        //         }
        //     ],
        // };
        // noticeData = data.notice_data;

        var noticeHtml = '';
        for (let i = 0; i < noticeData.list.length; i++) {
            var item = noticeData.list[i];
            noticeHtml += '<li>\n' +
                '                    <a class="'+item.class+'" data-title="'+item.atitle+'" href="'+Fast.api.fixurl(item.url)+'">\n' +
                '                        <div class="icon">\n' +
                '                            <img class="logo_img" src="'+Fast.api.cdnurl('/assets/addons/notice/img/icon2.png')+'">\n' +
                '                        </div>\n' +
                '                        <div class="text">'+item.title+'</div>\n' +
                '                        <div class="time">'+item.time+'</div>\n' +
                '                    </a>\n' +
                '                </li>';
        }
        if (!noticeHtml) {
            noticeHtml = '<div class="tip-text text-center" style="color: #ffffff">暂无待办</div>';
        }
        $('#notice-two ul').html(noticeHtml);
        if (noticeData.num > 0) {
            $('.notice-num').show().text(noticeData.num);
        } else {
            $('.notice-num').hide().text(noticeData.num);
        }

        // var waitData = {
        //     num: 0,
        //     list: [
        //         {
        //             'title': '待办aaa',
        //             'time':'xxx',
        //             'url':'server/order',
        //             'class': 'btn-addtabs'
        //         },
        //         {
        //             'title': '待办8988',
        //             'time':'xxx',
        //             'url':'',
        //         }
        //     ],
        // };
        // waitData = data.wait_data;


        var waitHtml = '';
        for (let i = 0; i < waitData.list.length; i++) {
            var item = waitData.list[i];
            waitHtml += '<li>\n' +
                '                    <a class="'+item.class+'" data-title="'+item.atitle+'" href="'+Fast.api.fixurl(item.url)+'">\n' +
                '                        <div class="icon">\n' +
                '                            <img class="logo_img" src="'+Fast.api.cdnurl('/assets/addons/notice/img/icon1.png')+'">\n' +
                '                        </div>\n' +
                '                        <div class="text">'+item.title+'</div>\n' +
                '                        <div class="time">'+item.time+'</div>\n' +
                '                    </a>\n' +
                '                </li>';
        }
        if (!waitHtml) {
            waitHtml = '<div class="tip-text text-center" style="color: #ffffff">暂无待办</div>';
        }
        $('#notice-one ul').html(waitHtml);
        if (waitData.num > 0) {
            $('.wait-num').show().text(waitData.num);
        } else {
            $('.wait-num').hide().text(waitData.num);
        }

        var allNoticeAllNum = waitData.num + noticeData.num;
        if (allNoticeAllNum > 0) {
            $('#notice-all-num').show().text(allNoticeAllNum);
        } else {
            $('#notice-all-num').hide().text(allNoticeAllNum);
        }
    }
};


require([], function (undefined) {
    // ajax轮询
    ajaxInit();

    wsInit();

    // 后台绑定按钮
    if (Config.modulename == 'admin' && Config.controllername == 'general.profile' && Config.actionname == 'index') {
        $('[type="submit"]').before('<button style="margin-right: 5px;" type="button" class="btn btn-primary btn-dialog"  data-url="notice/admin_mptemplate/bind">模版消息(公众号)</button>');
    }

});
// 手机端左右滑动切换菜单栏
if ('ontouchstart' in document.documentElement) {
    var startX, startY, moveEndX, moveEndY, relativeX, relativeY, element;
    element = $('body', top.document);
    $("body").on("touchstart", function (e) {
        startX = e.originalEvent.changedTouches[0].pageX;
        startY = e.originalEvent.changedTouches[0].pageY;
    });
    $("body").on("touchend", function (e) {
        moveEndX = e.originalEvent.changedTouches[0].pageX;
        moveEndY = e.originalEvent.changedTouches[0].pageY;
        relativeX = moveEndX - startX;
        relativeY = moveEndY - startY;

        // 判断标准
        //右滑
        if (relativeX > 45) {
            if ((Math.abs(relativeX) - Math.abs(relativeY)) > 50) {
                element.addClass("sidebar-open");
            }
        }
        //左滑
        else if (relativeX < -45) {
            if ((Math.abs(relativeX) - Math.abs(relativeY)) > 50) {
                element.removeClass("sidebar-open");
            }
        }
    });
}
});
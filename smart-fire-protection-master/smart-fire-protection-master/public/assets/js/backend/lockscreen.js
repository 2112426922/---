define(['jquery', 'bootstrap', 'backend', 'adminlte', 'form'], function ($, undefined, Backend, AdminLTE, Form) {
    var Controller = {
        lock: function () {
            //让错误提示框居中
            Fast.config.toastr.positionClass = "toast-top-center";
            if (Config.console) {
                Toastr.warning('禁止访问开发者工具,请关闭后重试');
            }
            //本地验证未通过时提示
            $("#lockscreen-form").data("validator-options", {
                invalid: function (form, errors) {
                    $.each(errors, function (i, j) {
                        Toastr.error(j);
                    });
                },
                target: '#errtips'
            });
            //阻止页面后退
            if (window.history && window.history.pushState) {
                $(window).on('popstate', function () {
                    window.history.pushState('forward', null, '#');
                    window.history.forward(1);
                });
            }
            window.history.pushState('forward', null, '#'); //在IE中必须得有这两行
            window.history.forward(1);

            //为表单绑定事件
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent("form[role=form]", function (data) {
                    location.href = Backend.api.fixurl(data.url);
                });
            }
        }
    };
    return Controller;
});
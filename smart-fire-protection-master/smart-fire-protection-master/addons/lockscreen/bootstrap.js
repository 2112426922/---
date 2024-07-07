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
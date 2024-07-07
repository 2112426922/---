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
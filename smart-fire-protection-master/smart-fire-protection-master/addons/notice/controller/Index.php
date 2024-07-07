<?php

namespace addons\notice\controller;

use addons\notice\library\Service;
use app\admin\model\notice\AdminMptemplate;
use EasyWeChat\Factory;
use think\addons\Controller;
use think\Cache;

class Index extends Controller
{

    protected $noNeedLogin = ['*'];

    public function index()
    {
        $this->error("当前插件暂无前台页面");
    }


    // 公众号授权页面
    public function mpauth()
    {
        $mark = input('mark');
        $adminId = cache($mark);
        if (!$adminId) {
            $this->error('二维码已过期，请重新扫描', '', '', 99999);
        }

        if (!input('confirm')) {
            return $this->fetch();
        }
        $app = Service::getOfficialAccount();

        $url = addon_url('notice/index/getmpauth', ['mark' => input('mark')], true, true);

        $response = $app->oauth->scopes(['snsapi_userinfo'])
            ->redirect($url);

        $url = $response->getTargetUrl();
        Header("Location: $url");
        exit();
    }

    // 公众号授权获取用户信息
    public function getmpauth()
    {
        $app = Service::getOfficialAccount();
        $oauth = $app->oauth;
        $msg = '绑定成功';


        // 获取 OAuth 授权结果用户信息

        $user = $oauth->user();
        $user = $user->toArray();
        $openid = $user['original']['openid'] ?? '';
        $nickname = $user['original']['nickname'] ?? '';
        $avatar = $user['original']['headimgurl'] ?? '';
        $unionid = $user['original']['unionid'] ?? '';

        $mark = input('mark');
        $adminId = cache($mark);
        if (!$adminId) {
            $this->error('二维码已过期，请重新扫描', '', '', 99999);
        }
        $exist = \app\admin\model\notice\AdminMptemplate::where('admin_id', $adminId)->find();
        if ($exist) {
            $msg = '该账号已被绑定过';
        } else {
            AdminMptemplate::create([
                'admin_id' => $adminId,
                'openid' => $openid,
                'nickname' => $nickname,
                'avatar' => $avatar,
                'unionid' => $unionid
            ]);
        }
        Cache::rm($mark);

        $this->assign('msg', $msg);
        // 绑定后台账号
        return $this->fetch();
    }
}

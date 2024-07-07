<?php
/**
 * QQ: 1123416584
 * Time: 2022/4/1 1:45 下午
 */

namespace addons\notice\controller;


use app\admin\library\Auth;
use GatewayClient\Gateway;
use think\Cache;
use think\Cookie;
use think\Request;
use think\Session;

class Ws extends \app\common\controller\Api
{
    protected $noNeedLogin = ['bind', 'bindadmin'];


    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if (!$this->request->isPost()) {
            $this->error('请求方式错误');
        }
    }

    /**
     * 绑定前台
     */
    public function bind()
    {
        $client_id = input('client_id');
        $auth = $this->auth;

        if (!$auth->isLogin()) {
            $this->error('未登录');
        }

        $uid = 'user_'.$auth->id;
        Gateway::bindUid($client_id, $uid);

        $this->success('绑定成功');
    }

    /**
     * 绑定后台
     */
    public function bindAdmin()
    {
        $client_id = input('client_id');
        $adminAuth = Auth::instance();

        if (!$adminAuth->isLogin()) {
            $this->error('未登录');
        }

        $uid = 'admin_'.$adminAuth->id;
        Gateway::bindUid($client_id, $uid);

        $this->success('绑定成功');
    }


}
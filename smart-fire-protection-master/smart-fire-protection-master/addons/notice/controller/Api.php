<?php
namespace addons\notice\controller;


use addons\notice\library\Service;
use app\admin\library\Auth;
use app\admin\model\notice\NoticeEvent;
use app\admin\model\notice\NoticeTemplate;
use think\Cache;

class Api extends \app\common\controller\Api
{
    protected $noNeedRight = ['*'];

    protected $noNeedLogin = ['cache', 'miniapptemplate', 'getminiappopenid'];

    // 未读消息数量
    public function unread()
    {
        $user = $this->auth->getUser();
        $count = \app\admin\model\notice\Notice::where('to_id', $user['id'])
            ->where('platform', 'user')
            ->where('type','msg')
            ->order('id', 'desc')
            ->whereNull('readtime')
            ->count();

        $this->success('', $count);
    }

    // 我的站内消息
    public function index()
    {
        $user = $this->auth->getUser();
        $list = \app\admin\model\notice\Notice::where('to_id', $user['id'])
            ->where('platform', 'user')
            ->where('type','msg')
            ->order('id', 'desc')
            ->paginate();

        $is = true;
        if ($is) {
            \app\admin\model\notice\Notice::where('id', 'in',array_column($list->items(), 'id'))
                ->update(['readtime' => time()]);
        }

        $this->success('', $list);
    }

    // 标记为已读
    public function mark()
    {
        $user = $this->auth->getUser();


        $where = [];
        if (input('id')) {
            $where['id'] = input('id');
        }

        $count = \app\admin\model\notice\Notice::where('to_id', $user['id'])
            ->where($where)
            ->where('platform', 'user')
            ->where('type','msg')
            ->order('id', 'desc')
            ->whereNull('readtime')
            ->update(['readtime' => time()]);

        $this->success('', $count);
    }

    // 获取最新一条未读数据
    public function statistical()
    {
        $user = $this->auth->getUserInfo();
        $statisticalTime = Cache::get('notice_user_statistical_time_'.$user['id'], 0);
        $new = \app\admin\model\notice\Notice::where('to_id', $user['id'])
            ->where('platform', 'user')
            ->where('type','msg')
            ->order('id', 'desc')
            ->where('createtime','>', $statisticalTime)
            ->whereNull('readtime')
            ->find();
        if ($new) {
            Cache::set('notice_user_statistical_time_'.$user['id'], time());
        }
        $data = [
            'num' => \app\admin\model\notice\Notice::where('to_id', $user['id'])
                ->where('platform', 'user')
                ->where('type','msg')
                ->order('id', 'desc')
                ->whereNull('readtime')
                ->count()
            ,
            'new' => $new,
        ];

        $this->success('', $data );
    }

    // 缓存最后提示站内消息时间
    public function cache()
    {
        if (!$this->request->isPost()) {
            $this->error('请求方式错误');
        }

        $type = input('module', 'admin');
        $time = input('time');
        if ($type == 'admin') {
            $adminAuth = Auth::instance();
            if (!$adminAuth->isLogin()) {
                $this->error('未登录');
            }
            Cache::set('notice_admin_statistical_time_'.$adminAuth->id, $time);
        }

        if ($type == 'index') {
            $auth = $this->auth;
            if (!$auth->isLogin()) {
                $this->error('未登录');
            }

            Cache::set('notice_user_statistical_time_'.$auth->id, $time);
        }

        $this->success('ok');
    }

    /**
     * 获取微信小程序订阅消息模板
     */
    public function miniappTemplate()
    {
        $params = $this->request->only(['event', 'platform']);

        $params['platform'] = $params['platform'] ?? 'user';
        $params['event'] = explode(',', $params['event']);

        $notice_event_ids = NoticeEvent::where('event', 'in', $params['event'])->where("find_in_set('miniapp', type)")->column('id');

        $template = NoticeTemplate::where('platform', $params['platform'])
            ->where('notice_event_id', 'in', $notice_event_ids)
            ->where('type', 'miniapp')
            ->column('mptemplate_id');
        $template = array_values(array_filter($template));

        $this->success('', [
            'template' => $template
        ]);
    }

    /**
     * 获取微信小程序openid -- 测试使用 -- 非测试情况下请关闭
     */
    public function getMiniappOpenid()
    {
        $config = get_addon_config('notice');
        if (!$config['is_test_miniapp_template']) {
            $this->error('配置未启微信订阅消息测试模式');
        }

        $app = Service::getMini();

        $res = $app->auth->session(input('code'));

        $this->success('', $res);
    }
}
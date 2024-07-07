<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\admin\model\Admin;
use think\Config;
use think\Hook;
use think\Session;
use think\Validate;

/**
 * 锁屏管理
 *
 * @icon fa fa-circle-o
 */
class Lockscreen extends Backend
{

    protected $model = null;
    protected $layout = null;
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $cdnurl = Config::get('site.cdnurl');
        $this->view->replace('__ADDON__', $cdnurl . "/assets/addons/lockscreen");
    }

    /**
     * 锁屏页面
     */
    public function lock()
    {
        if (stripos($this->request->server('HTTP_REFERER'), 'index/login') !== false) {
            $this->redirect('index/index');
        }
        $url = $this->request->has('url');
        if ($this->request->isPost()) {
            $this->unLock();
        }
        $background = Config::get('fastadmin.login_background');
        if($background !== "")
            $background = stripos($background, 'http') === 0 ? $background : config('site.cdnurl') . $background;
        
        $this->view->assign('background', $background);
        $this->view->assign('title', __('Locked'));
        if ($url) {
            $this->assignconfig('url', $url);
        }
        Hook::listen("admin_lockscreen_lock", $this->request);
        Session::set('lockscreen', ['failure' => 0, 'status' => true]);
        return $this->view->fetch();
    }

    /**
     * 解锁控制
     */
    public function unLock()
    {
        if ($this->request->isPost()) {
            $url = $this->request->get('url', 'index/index');
            $result = $this->checkPassword();
            if ($result === true) {
                Hook::listen("admin_lockscreen_unlock", $this->request);
                $this->success(__('Unlocked Success'), null, ['url' => $url]);
            } else {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : __('Password is incorrect');
                $this->error($msg, null, ['url' => $url]);
            }
        }
    }

    /**
     * 设置锁定状态
     */
    public function setLock()
    {
        if ($this->request->isPost()) {
            Session::set('lockscreen', ['failure' => 0, 'status' => true]);
            $this->success();
        }
    }

    /**
     * 设置未锁定状态
     */
    public function setUnlock()
    {
        if ($this->request->isPost()) {
            Session::delete('lockscreen');
            $this->success();
        }
    }

    /**
     * 获取锁定状态
     * @return JSON
     */
    public function getLock()
    {
        if ($this->request->isGet()) {
            return json(Session::get('lockscreen') ?: ['failure' => 0, 'status' => false]);
        }
    }

    /**
     * 登录密码鉴权
     * @return boolean
     */
    protected function checkPassword()
    {
        $url = $this->request->get('url', 'index/index');
        $password = $this->request->post('password');
        $lockscreen = Session::get('lockscreen') ?: ['failure' => 0, 'status' => true];
        $config = get_addon_config('lockscreen');
        $rule = [
            'password' => 'require|length:3,30',
        ];
        $data = [
            'password' => $password,
        ];
        $validate = new Validate($rule, [], ['password' => __('Password')]);
        $result = $validate->check($data);
        if (!$result) {
            $this->error($validate->getError(), null, ['url' => $url]);
        }
        if ($config["failure_retry"] > 0 && $lockscreen['failure'] >= $config["failure_retry"]) {
            $this->auth->logout();
            Session::delete('lockscreen');
            $this->error(__('Too many unlock failures, please log in again'), 'index/login', ['url' => $url]);
        }
        $admin = Admin::get($this->auth->id);
        if ($admin->password != md5(md5($password) . $admin->salt)) {
            $lockscreen['failure']++;
            Session::set('lockscreen', ['failure' => $lockscreen['failure'], 'status' => true]);
            $this->auth->setError('Password is incorrect');
            return false;
        }
        Session::delete('lockscreen');
        return true;
    }

}

<?php

namespace addons\lockscreen;

use think\Addons;
use think\Session;
use think\Request;

/**
 * 插件
 */
class Lockscreen extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {

        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {

        return true;
    }

    public function responseSend()
    {
        $except_controller = ['index'];
        $except_action = ['logout'];
        $request = Request::instance();
        if (in_array(strtolower($request->module()), ['admin']) && !(in_array(strtolower($request->controller()), $except_controller) && in_array(strtolower($request->action()),$except_action))) {
            $config = $this->getConfig();
            $session = Session::get('lockscreen') ?: ['failure' => 0, 'status' => false];
            if (!in_array(strtolower($request->controller()), ['lockscreen','ajax'])) {
                if (!in_array(strtolower($request->action()), ['login'])) {
                    if ($config['password'] == '1') {
                        if ($session['status']) {
                            $url = url('Lockscreen/lock', 'url=' . url('index/index'));
                            echo "<script type='text/javascript'>";
                            echo "top.location.href='" . $url . "'";
                            echo "</script>";
                        }
                    }
                }
            }
        }
    }

    /**
     * 实现钩子方法
     * @return mixed
     */
    public function configInit(&$params)
    {
        if (in_array(strtolower($params['modulename']), ['admin'])) {
            $config = $this->getConfig();
            $session = Session::get('lockscreen') ?: ['failure' => 0, 'status' => false];
            if (!in_array(strtolower($params['controllername']), ['lockscreen'])) {
                if (!in_array(strtolower($params['actionname']), ['login'])) {
                    $params['lockscreen']['session'] = $session;
                }
            }
            $config['baseUrl']= url('lockscreen/lock');
            $config['controller'] = request()->controller();
            $config['action'] = request()->action();
            $config['module'] = request()->module();
            $params['lockscreen'] = $config;
        }
    }

    public function adminLoginInit(\think\Request &$request)
    {
        Session::delete('lockscreen');
    }

    public function lockscreenhook($param)
    {
        $url = url('lockscreen/lock');
        $html = '<li class="hidden-xs"><a href="' . $url . '"><i class="fa fa-lock"></i></a></li>';
        echo $html;
    }
}

<?php

namespace addons\adminlogin;

use addons\adminlogin\library\Service;
use think\Addons;
use think\Loader;
use think\Validate;

/**
 * 插件
 */
class Adminlogin extends Addons
{

    public function adminNologin()
    {
        Service::error();
    }


    public function moduleInit() {
        // 判断是否关闭fast自带登录
        $addonConfig = get_addon_config('adminlogin');
        $is = $addonConfig['close_fast'];
        if (!$is) {
            return true;
        }
        if (request()->module() != 'admin') {
            return true;
        }

        $controllername = Loader::parseName(request()->controller());
        $actionname = strtolower(request()->action());
        $path = str_replace('.', '/', $controllername) . '/' . $actionname;
        if ($path == 'index/login') {
            Service::error();
        }
    }

    /**
     * 脚本替换
     */
    public function viewFilter(&$content)
    {
        $info = get_addon_info('clicaptcha');
        if($info && $info['state']){
            $module = strtolower(request()->module());
            if (($module == 'adminlogin' && config('fastadmin.user_register_captcha') == 'text') || ($module == 'admin' && config('fastadmin.login_captcha')) || ($module == 'store' && config('fastadmin.login_captcha'))) {
                $content = preg_replace_callback('/<!--@CaptchaBegin-->([\s\S]*?)<!--@CaptchaEnd-->/i', function ($matches) {
                    return '<!--@CaptchaBegin--><div><input type="hidden" name="captcha" /></div><!--@CaptchaEnd-->';
                }, $content);
            }
        }
    }

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

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        
        return true;
    }

}

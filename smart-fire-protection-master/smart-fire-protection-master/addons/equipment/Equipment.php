<?php

namespace addons\equipment;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Equipment extends Addons
{

    
    /**
     * 增加命令
     */
    public function appInit($param)
    {
        if (request()->isCli()) {
            \think\Console::addDefaultCommands([
                'addons\equipment\library\GatewayWorker\start'
            ]);
        }
    }

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = include ADDON_PATH . "equipment" . DS . 'config' . DS . "menu.php";
        $config_file = ADDON_PATH . "equipment" . DS . 'config' . DS . "menu.php";
        if (is_file($config_file)) {
            $menu = include $config_file;
        }
        if ($menu) {
            Menu::create($menu);
        }
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete("equipment");
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable("equipment");
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable("equipment");
        return true;
    }
}

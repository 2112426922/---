<?php

namespace addons\screen;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Screen extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $config_file = ADDON_PATH . "screen" . DS . 'config' . DS . "menu.php";
        $menu = include $config_file;
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete("screen");
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable("screen");
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable("screen");
        return true;
    }

}

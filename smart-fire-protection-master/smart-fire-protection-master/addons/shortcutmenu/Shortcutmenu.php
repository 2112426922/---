<?php

namespace addons\shortcutmenu;

use app\common\library\Menu;
use think\Addons;


/**
 * import 可视化数据导入辅助
 */
class Shortcutmenu extends Addons
{
    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name' => 'shortcutmenu/menu',
                'title' => '快捷菜单',
                'remark' => '为导航菜单设置快捷访问菜单',
                'icon' => 'fa fa-circle-o',
                'ismenu' => 1,
                'weigh' => 230,
                'sublist' => [
                    ['name' => 'shortcutmenu/menu/index', 'title' => '查看'],
                    ['name' => 'shortcutmenu/menu/edit', 'title' => '设置']
                ],
            ]
        ];

        //$menu=[];

        $config_file = ADDON_PATH . "shortcutmenu" . DS . 'config' . DS . "shortcutmenu.php";

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
        Menu::delete('shortcutmenu/menu');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        Menu::enable('shortcutmenu/menu');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        Menu::disable('shortcutmenu/menu');
    }

}


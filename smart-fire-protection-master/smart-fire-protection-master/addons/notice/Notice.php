<?php

namespace addons\notice;

use addons\notice\library\GatewayTool;
use addons\notice\library\NoticeClient;
use app\admin\library\Auth;
use app\common\library\Menu;
use think\Addons;
use think\Request;

/**
 * 插件
 */
class Notice extends Addons
{


    /**
     * 1.0.5
     * 修复socket模式下消息面版无法使用问题
     *
     *
     *1.0.4
     * 1. 新增 短信通知
     * 2. 修复 后台编辑邮箱模板
     * 3. 优化 pc前台展示消息列表
     *
     *
     *
     * v1.0.3
     * 1. 新增 消息面板
     * 2. 新增 小程序订阅消息
     * 3. 新增 小程序订阅消息uni示例
     * 4. 修复 ws无权限创建文件夹
     * 5. 优化 前/后台展示消息列表
     *
     */

    /**
     * 应用初始化
     */
    public function appInit()
    {
        // 公共方法
        require_once __DIR__ . '/library/common/helper.php';


        if ($this->getConfig()['admin_real'] == 2 || $this->getConfig()['user_real'] == 2) {
            require_once __DIR__ . '/library/libs/GatewayClient/Gateway.php';
            GatewayTool::init();
        }

        if (request()->isCli()) {
            \think\Console::addDefaultCommands([
                'addons\notice\library\libs\GatewayWorker\server'
            ]);
        }
    }

    /**
     * @param $params
     */
    public function configInit(&$params)
    {
        $config = $this->getConfig();

        if ($config['wss_switch'] == 1) {
            $protocol = 'wss://';
        } else {
            $protocol = 'ws://';
        }
        $wsurl = $protocol .\request()->host() . ':' . $config['websocket_port'];


        $params['notice'] = [
            'admin_real' => $config['admin_real'],
            'wsurl' => $wsurl,
            'user_real_url' => $config['user_real_url'],
            'user_real' => $config['user_real'],
            'admin_check' => (Auth::instance())->check('notice/admin')
        ];
    }


    /**
     * 插件安装方法
     *
     * @return bool
     */
    public function install()
    {
        $config_file = ADDON_PATH . "notice" . DS . 'config' . DS . "menu.php";
        $menu = include $config_file;
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     *
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete("notice");
        Menu::delete("notice/admin");

        return true;
    }

    /**
     * 插件启用方法
     *
     * @return bool
     */
    public function enable()
    {
        Menu::enable("notice");
        Menu::enable("notice/admin");

        return true;
    }

    /**
     * 插件禁用方法
     *
     * @return bool
     */
    public function disable()
    {
        Menu::disable("notice");
        Menu::disable("notice/admin");

        return true;
    }

    /**
     * 会员中心边栏后
     *
     * @return mixed
     * @throws \Exception
     */
    public function userSidenavAfter()
    {
        $request = Request::instance();
        $actionname = strtolower($request->action());
        $data = [
            'actionname' => $actionname,
        ];

        return $this->fetch('view/hook/user_sidenav_after', $data);
    }


    /**
     * 发送消息
     */
    public function sendNotice($params)
    {
        return NoticeClient::instance()->trigger($params['event'], $params['params']);
    }
}

<?php
namespace addons\webscan;

use addons\webscan\library\ChallengeCollapsar;
use addons\webscan\library\Webscan as WebscanService;
use app\common\library\Menu;
use think\Addons;
use think\Exception;

/**
 * 插件
 */
class Webscan extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu=[];
        $config_file= ADDON_PATH ."webscan" . DS.'config'.DS. "menu.php";
        if (is_file($config_file)) {
            $menu = include $config_file;
        }
        if($menu){
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
        $info=get_addon_info('webscan');
        Menu::delete(isset($info['first_menu'])?$info['first_menu']:'webscan');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        $info=get_addon_info('webscan');
        Menu::enable(isset($info['first_menu'])?$info['first_menu']:'webscan');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        $info=get_addon_info('webscan');
        Menu::disable(isset($info['first_menu'])?$info['first_menu']:'webscan');
    }

    /**
     * 应用初始化标钩子
     */
    public function appInit(){

        //判断如果是cli模式直接返回
        if (preg_match("/cli/i", php_sapi_name())) return true;

        $config= $this->getConfig();
        //黑名单处理
        $ip=\request()->ip();

        if ($ip&&$config['webscan_black_ip']){
            $webscan_black_ip_arr=explode(PHP_EOL,$config['webscan_black_ip']);

            if (in_array($ip,$webscan_black_ip_arr)){
                $config['webscan_warn']=$config['black_warn'];
               (new  WebscanService($config))->webscanPape();
            }

        }

        //是否开启CC过滤
        if ($config['ccopen']==1){
            try{
                //CC攻击
                $ChallengeCollapsar=new ChallengeCollapsar($config);
                $ChallengeCollapsar->start();
            }catch (Exception $exception){
            }
        }

        if ($config['webscan_switch']==1){
            $webscan=new WebscanService($config);
            $webscan->start();
        }

    }
}

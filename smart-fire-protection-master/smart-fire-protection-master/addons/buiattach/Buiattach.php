<?php

namespace addons\buiattach;

use think\Addons;
use think\Request;
use think\Config;
use app\common\library\Menu;
use app\admin\library\buiattach\Imgcompress;

/**
 * 插件
 */
class Buiattach extends Addons
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
	
	public function configInit(&$params){
		$config = $this->getConfig();
		$params['buiattach'] = ['is_default' => $config['is_default']];
	}
	
	/**
	 * 添加附件后
	 */
    public function uploadAfter($attachment){
		$host  		= Request::instance()->domain();
		$config	    = $this->getConfig();
		if(!empty($config['is_compress']) && !empty($config['compress_scale'])){
			$attachment = json_decode($attachment,true);
			$image_full = sprintf("%s%s",$host,$attachment['url']);
			$image_path = sprintf("%s%s",$_SERVER['DOCUMENT_ROOT'],$attachment['url']);
			$pathinfo   = (pathinfo($image_full));
			if(array_key_exists('extension',$pathinfo)){
				$percent = trim($config['compress_scale']);
				$imgcompress = new Imgcompress($image_full, $percent, $image_path);
				$imgcompress->compressImg($image_path);
			}
		}
    }

	

}

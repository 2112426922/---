<?php
namespace app\admin\library\buiattach;

class Imgcompress {
	
	private $src;
	private $image;
	private $imageinfo;
	private $percent = 1;
	
	/**
     * 图片压缩
     * @param $src 源图
     * @param float $percent  压缩比例
     */
	public function __construct($src, $percent=1, $img_src = '') {
		$this->src = $src;
		$this->percent = $percent;
		$this->img_src = $img_src;
	}
	
	/** 高清压缩图片
     * @param string $saveName  提供图片名（可不带扩展名，用源图扩展名）用于保存。或不提供文件名直接显示
     */
	public function compressImg($saveName='') {
		$this->_openImage();
		if(!empty($saveName)) $this->_saveImage($saveName);
	}
	
	/**
     * 内部：打开图片
     */
	private function _openImage() {
		list($width, $height, $type, $attr) = getimagesize($this->src);
		$this->imageinfo = array(
		            'width'=>$width,
		            'height'=>$height,
		            'type'=>image_type_to_extension($type,false),
		            'attr'=>$attr
		        );
		$fun = "imagecreatefrom".$this->imageinfo['type'];
		$this->image = $fun($this->src);
		$this->_thumpImage();
	}
	
	/**
     * 内部：操作图片
     */
	private function _thumpImage() {
		$new_width = $this->imageinfo['width'] * $this->percent;
		$new_height = $this->imageinfo['height'] * $this->percent;
		$image_thump = imagecreatetruecolor($new_width,$new_height);
		imagecopyresampled($image_thump,$this->image,0,0,0,0,$new_width,$new_height,$this->imageinfo['width'],$this->imageinfo['height']);
		imagedestroy($this->image);
		$this->image = $image_thump;
	}
	
	/**
     * 输出图片:保存图片则用saveImage()
     */
	private function _showImage() {
		header('Content-Type: image/'.$this->imageinfo['type']);
		$funcs = "image".$this->imageinfo['type'];
		$funcs($this->image);
	}
	
	/**
     * 保存图片到硬盘：
     * @param  string $dstImgName  1、可指定字符串不带后缀的名称，使用源图扩展名 。2、直接指定目标图片名带扩展名。
     */
	private function _saveImage($dstImgName) {
		if(empty($dstImgName)) return false;
		$file=substr($dstImgName,0,strrpos($dstImgName,'/'));
		if(!is_dir($file)) {
			mkdir($file,0777);
		}
		$allowImgs = ['.jpg', '.jpeg', '.png', '.bmp', '.wbmp','.gif'];
		$dstExt =  strrchr($dstImgName ,".");
		$sourseExt = strrchr($this->src ,".");
		if(!empty($dstExt)) $dstExt =strtolower($dstExt);
		if(!empty($sourseExt)) $sourseExt =strtolower($sourseExt);
		if(!empty($dstExt) && in_array($dstExt,$allowImgs)) {
			$dstName = $dstImgName;
		} elseif(!empty($sourseExt) && in_array($sourseExt,$allowImgs)) {
			$dstName = $dstImgName.$sourseExt;
		} else {
			$dstName = $dstImgName.$this->imageinfo['type'];
		}
		$funcs = "image".$this->imageinfo['type'];
		$funcs($this->image,$dstName);
	}
}
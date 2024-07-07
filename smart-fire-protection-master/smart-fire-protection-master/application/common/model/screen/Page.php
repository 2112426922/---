<?php

namespace app\common\model\screen;

use think\Model;
use traits\model\SoftDelete;

class Page extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'screen_page';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['normal' => __('Status normal'), 'hidden' => __('Status hidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }


    public static function operGetDict($name = '')
    {
        $data = include ROOT_PATH.'addons/screen/config/dictionary.php';

        if ($name) {
            return $data[$name] ?? [];
        }
        return $data;
    }


}

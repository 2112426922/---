<?php

namespace app\admin\model\equipment;

use think\Model;
use traits\model\SoftDelete;

class Record extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'equipment_record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

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
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function user()
    {
        return $this->belongsTo(\app\admin\model\User::class, 'add_uid', 'id', [], 'LEFT')->field('id, nickname, mobile');
    }
}

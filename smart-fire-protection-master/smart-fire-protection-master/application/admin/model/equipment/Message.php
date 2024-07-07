<?php

namespace app\admin\model\equipment;

use think\Model;


class Message extends Model
{

    

    

    // 表名
    protected $name = 'equipment_message';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'updatetime_text'
    ];
    

    



    public function getUpdatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['updatetime']) ? $data['updatetime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setUpdatetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function equipment()
    {
        return $this->belongsTo('Equipment', 'code', 'equipment_code', [], 'LEFT')->setEagerlyType(0);
    }


    // public function archive()
    // {
    //     return $this->belongsTo('Archive', 'archive_id', 'id', [], 'LEFT')->setEagerlyType(0);
    // }
    public function archive()
    {
        return $this->belongsTo(Archive::class, 'archive_id');
    }
}

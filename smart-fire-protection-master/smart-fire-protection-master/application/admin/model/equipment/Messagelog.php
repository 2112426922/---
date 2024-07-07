<?php

namespace app\admin\model\equipment;

use think\Model;


class Messagelog extends Model
{

    

    

    // 表名
    protected $name = 'equipment_messagelog';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    







    // public function archive()
    // {
    //     return $this->belongsTo('Archive', 'archive_id', 'id', [], 'LEFT')->setEagerlyType(0);
    // }

    public function archive()
    {
        return $this->belongsTo(Archive::class, 'archive_id');
    }


    public function equipment()
    {
        return $this->belongsTo('Equipment', 'code', 'equipment_code', [], 'LEFT')->setEagerlyType(0);
    }
}

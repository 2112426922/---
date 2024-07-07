<?php

namespace app\admin\model\shortcutmenu;

use think\Model;


class Menu extends Model
{


    // 表名
    protected $name = 'shortcutmenu';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];


    public function rule()
    {
        return $this->belongsTo('app\admin\model\auth\Rule', 'auth_rule_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}

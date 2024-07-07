<?php

namespace app\admin\model\equipment;

use think\Model;
use traits\model\SoftDelete;

class PlanUser extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_plan_user';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];

    public function user()
    {
        return $this->belongsTo(\app\admin\model\User::class, 'user_id', 'id', [], 'LEFT')->field('id, nickname, mobile');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
        // return $this->hasOne(Plan::class, 'plan_id');
    }

    public function addUsers($planId, $users)
    {
        $list = [];
        $users = explode(',', $users);
        foreach ($users as $userId) {
            $list[] = [
                'plan_id' => $planId,
                'user_id' => $userId
            ];
        }

        $result = $this->saveAll($list);
        if (!$result) {
            return $this->getError();
        }

        return true;
    }
}

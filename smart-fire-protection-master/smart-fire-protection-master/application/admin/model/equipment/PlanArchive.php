<?php

namespace app\admin\model\equipment;

use think\Model;
use traits\model\SoftDelete;

class PlanArchive extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_plan_archive';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];

    public function archive()
    {
        return $this->belongsTo(Archive::class, 'archive_id')->field('id, model, name');
    }

    public function addArchives($planId, $archives)
    {
        $list = [];
        $archives = explode(',', $archives);
        foreach ($archives as $archiveId) {
            $list[] = [
                'plan_id' => $planId,
                'archive_id' => $archiveId
            ];
        }

        $result = $this->saveAll($list);
        if (!$result) {
            return $this->getError();
        }

        return true;
    }
}

<?php

namespace app\admin\model\equipment;

use think\Model;
use traits\model\SoftDelete;

class PlanTask extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_plan_task';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text',
        'duetime_text'
    ];


    public function getStatusList()
    {
        return ['pending' => __('Pending'), 'finish' => __('Finish'), 'overdue' => __('Overdue')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getDuetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['duetime']) ? $data['duetime'] : '');
        return is_numeric($value) ? date("Y年m月d日", $value) : $value;
    }

    protected function setDuetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function eagerlyEquipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function user()
    {
        return $this->belongsTo(\app\admin\model\User::class, 'task_uid', 'id', [], 'LEFT')->field('id, nickname, mobile');
    }

    public function addTasks($planId, $archives, $periodicity, $firstDuetime, $lastDuetime)
    {
        $list = [];
        $days = ($lastDuetime - $firstDuetime) / 86400;
        $taskCount = ceil($days / $periodicity) + 1;

        $equipmentModel = new Equipment();
        if (is_string($archives)) $archives = explode(',', $archives);
        $equipmentIds = $equipmentModel->whereIn('archive_id', $archives)->where('work_status', '<>', 'scrapped')->column('id');
        $codings = $equipmentModel->createCoding($taskCount * count($equipmentIds), 'T');

        $num = 0;
        for ($i = 0; $i < $taskCount; $i++) {
            $duetime = $firstDuetime + $i * $periodicity * 86400;
            $starttime = $duetime - $periodicity * 86400 + 1;
            if ($duetime > $lastDuetime) $duetime = $lastDuetime;

            foreach ($equipmentIds as $equipmentId) {
                $list[] = [
                    'coding' => $codings[$num],
                    'plan_id' => $planId,
                    'equipment_id' => $equipmentId,
                    'starttime' => $starttime,
                    'duetime' => $duetime
                ];
                $num++;
            }
        }

        $result = $this->saveAll($list);
        if (!$result) {
            return $this->getError();
        }

        return true;
    }
}

<?php

namespace app\admin\model\equipment;

use think\Model;
use traits\model\SoftDelete;

class Repair extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_repair';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'registertime_text',
        'assigntime_text',
        'repairtime_text',
        'consuming_text',
        'status_text'
    ];

    public function getStatusList()
    {
        return ['pending' => __('Pending'), 'registered' => __('Registered'), 'repaired' => __('Repaired'), 'scrapped' => __('Scrapped')];
    }

    public function getTabsList()
    {
        return ['pending' => __('Pending'), 'registered' => __('Registered_tab')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getRegistertimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['registertime']) ? $data['registertime'] : '');
        return is_numeric($value) ? date("Y年m月d日 H:i", $value) : $value;
    }

    public function getAssigntimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['assigntime']) ? $data['assigntime'] : '');
        return (is_numeric($value) && $value > 0) ? date("Y年m月d日 H:i", $value) : Null;
    }

    public function getRepairtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['repairtime']) ? $data['repairtime'] : '');
        return (is_numeric($value) && $value > 0) ? date("Y年m月d日 H:i", $value) : Null;
    }

    public function getConsumingTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['consuming']) ? $data['consuming'] : '');

        $timeArr = ["years" => 0, "days" => 0, "hours" => 0, "minutes" => 0, "seconds" => 0];
        $timeText = '';
        if ($value >= 31556926) {
            $year = floor($value / 31556926);
            $value = ($value % 31556926);

            if ($year > 0) $timeText .= $year . "年 ";
        }
        if ($value >= 86400) {
            $days = floor($value / 86400);
            $value = ($value % 86400);

            if ($days > 0) $timeText .= $days . "天 ";
        }
        if ($value >= 3600) {
            $hours = floor($value / 3600);
            $value = ($value % 3600);

            if ($hours > 0) $timeText .= $hours . "小时 ";
        }
        if ($value >= 60) {
            $minutes = floor($value / 60);
            $value = ($value % 60);

            if ($minutes > 0) $timeText .= $minutes . "分 ";
        }
        $seconds = floor($value);
        if ($seconds > 0) {
            $timeText .= $seconds . "秒";
        } else {
            if (empty($timeText)) {
                $timeText = "0秒";
            }
        }

        return $timeText;
    }

    protected function setRegistertimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setAssigntimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setRepairtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function archive()
    {
        return $this->belongsTo(Archive::class, 'archive_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function registerUser()
    {
        return $this->belongsTo(\app\admin\model\User::class, 'register_uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function repairUser()
    {
        return $this->belongsTo(\app\admin\model\User::class, 'repair_uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function failureCause()
    {
        return $this->belongsTo(FailureCause::class, 'failure_cause_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function generateCode($amount)
    {
        $beginNum = $this->withTrashed()->whereTime('createtime', 'today')->count() + 1;

        $generateCodes = [];
        $codeDate = date('ymd');
        for ($i = 0; $i < $amount; $i++) {
            $generateCodes[] = 'R' . $codeDate . '-' . str_pad($beginNum, 3, "0", STR_PAD_LEFT);
            $beginNum++;
        }

        return $generateCodes;
    }

    public function assignStaff($id, $userId)
    {
        $repair = $this->find($id);
        if (in_array($repair['status'], ['repaired', 'scrapped'])) {
            return '维修工单已完成，不允许再指派！';
        }
        if ($repair['repair_uid'] == $userId) {
            return '无需重复指派同一人员！';
        }

        $data = [
            'id' => $id,
            'repair_uid' => $userId,
            'assigntime' => time(),
            'status' => 'registered'
        ];
        $result = $this->isUpdate(true)->update($data);
        if (!$result) {
            return $this->getError();
        }

        $reminderUsersModel = new ReminderUsers();
        $reminderUsersModel->toRemindSMS('assignRepair', $id);

        return true;
    }
}

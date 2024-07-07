<?php

namespace app\admin\model\equipment;

use addons\qcloudsms\library\SmsMultiSender;
use app\admin\model\User;
use think\Model;
use traits\model\SoftDelete;

class ReminderUsers extends Model
{

    use SoftDelete;

    // 表名
    protected $name = 'equipment_reminder_users';

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
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function multiStaff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function toRemindSMS($type, $id = '')
    {
        $result = true;
        $equipmentConfig = get_addon_config('equipment');
        if ($type == 'newRepair') {
            if ($equipmentConfig['remind_assign_repair'] == 1) {
                $staffIds = $this->where(['type' => 'assign_repair', 'status' => 'normal'])->column('staff_id');
                $userIds = Staff::where(['id' => ['in', $staffIds], 'status' => 'normal'])->column('user_id');
                $mobiles = User::where(['id' => ['in', $userIds]])->column('mobile');
                if (!empty($mobiles)) {
                    $result = $this->sendSMS($mobiles, 'remindAssignRepair');
                }
            }
            if ($equipmentConfig['remind_receive_repair'] == 1) {
                $departmentIds = Department::where(['equipment_manage' => 1, 'status' => 'normal'])->column('id');
                $userIds = Staff::where(['department_id' => ['in', $departmentIds], 'status' => 'normal'])->column('user_id');
                $mobiles = User::where(['id' => ['in', $userIds]])->column('mobile');
                if (!empty($mobiles)) {
                    $result = $this->sendSMS($mobiles, 'remindReceiveRepair');
                }
            }
        }
        if ($type == 'assignRepair' && $equipmentConfig['remind_deal_repair'] == 1) {
            $repairUid = Repair::where('id', $id)->value('repair_uid');
            $mobile = User::where('id', $repairUid)->value('mobile');
            if ($mobile) {
                $result = $this->sendSMS($mobile, 'remindDealRepair');
            }
        }

        return $result;
    }

    private function sendSMS($mobiles, $type)
    {
        if (is_string($mobiles)) {
            $mobiles = explode(',', $mobiles);
        }

        $params = [];
        $smsConfig = get_addon_config('qcloudsms');
        switch ($type) {
            case 'remindAssignRepair':
                $templateID = $smsConfig['template']['repair'];
                $params = ["待指派"];
                break;
            case 'remindReceiveRepair':
                $templateID = $smsConfig['template']['repair'];
                $params = ["可认领"];
                break;
            case 'remindDealRepair':
                $templateID = $smsConfig['template']['repair'];
                $params = ["被指派"];
                break;
            default:
                break;
        }

        $sender = new SmsMultiSender($smsConfig['appid'], $smsConfig['appkey']);
        $result = $sender->sendWithParam("86", $mobiles, $templateID, $params, $smsConfig['sign'], "", "");

        $rsp = json_decode($result, true);
        if ($rsp['result'] == 0 && $rsp['errmsg'] == 'OK') {
            return true;
        } else {
            return false;
        }
    }
}

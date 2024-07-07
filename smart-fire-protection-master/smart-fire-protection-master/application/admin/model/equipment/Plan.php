<?php

namespace app\admin\model\equipment;

use think\Db;
use think\Exception;
use think\Model;
use traits\model\SoftDelete;

class Plan extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_plan';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'first_duetime_text',
        'last_duetime_text',
        'status_text'
    ];


    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getFirstDuetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['first_duetime']) ? $data['first_duetime'] : '');
        return is_numeric($value) ? date("Y年m月d日", $value) : $value;
    }

    public function getLastDuetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['last_duetime']) ? $data['last_duetime'] : '');
        return is_numeric($value) ? date("Y年m月d日", $value) : $value;
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setFirstDuetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setLastDuetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function planArchives()
    {
        return $this->hasMany('PlanArchive', 'plan_id')->with('archive')->field('plan_id, archive_id');
    }

    public function planFields()
    {
        return $this->hasMany('PlanField', 'plan_id');
    }

    public function planUsers()
    {
        return $this->hasMany('PlanUser', 'plan_id')->with('user')->field('plan_id, user_id');
    }

    public function addPlan($type, $data)
    {
        Db::startTrans();
        try {
            $equipmentModel = new Equipment();

            $archives = $data['archive_range'];
            $fields = json_decode($data['plan_fields'], true);
            $users = $data['plan_user'];

            $firstDuetime = str_replace(['年', '月', '日'], ['-', '-', ''], $data['first_duetime']);
            $lastDuetime = str_replace(['年', '月', '日'], ['-', '-', ''], $data['last_duetime']);
            $firstDuetime = strtotime($firstDuetime . ' 23:59:59');
            $lastDuetime = strtotime($lastDuetime . ' 23:59:59');

            if (empty($fields)) {
                throw new Exception(__('Parameter %s can not be empty', __('PlanField')));
            }
            if ($lastDuetime < $firstDuetime) {
                throw new Exception(__('The plan end date should not be earlier than the first implementation date'));
            }

            // 排查是否区间内有重叠设备
            $planArchiveModel = new PlanArchive();
            $planTaskModel = new PlanTask();
            $planIds = $planTaskModel->whereTime('duetime', 'between', [$firstDuetime, $lastDuetime])->column('plan_id');
            $planIds = array_unique($planIds);
            $planIds = $this->where('type', $type)->whereIn('id', $planIds)->column('id');
            $issetArchiveIds = $planArchiveModel->whereIn('plan_id', $planIds)->whereIn('archive_id', $archives)->column('archive_id');
            if (count($issetArchiveIds) > 0) {
                $archiveModel = new Archive();
                $archiveList = $archiveModel->whereIn('id', $issetArchiveIds)->column('concat("[", model, "] ", name)');
                $archiveList = implode('、', $archiveList);
                throw new Exception(__('%s Already exists in other plans', $archiveList));
            }

            // 执行添加
            $codings = $equipmentModel->createCoding(1, 'P');
            $plan = [
                'coding' => $codings[0],
                'name' => $data['name'],
                'periodicity' => $data['periodicity'],
                'first_duetime' => $firstDuetime,
                'last_duetime' => $lastDuetime,
                'type' => $type
            ];
            $result = $this->isUpdate(false)->data($plan)->save();
            if (!$result) {
                throw new Exception($this->getError());
            }
            $planId = $this->id;

            // 计划关联设备档案
            $planArchiveModel = new PlanArchive();
            $archiveResult = $planArchiveModel->addArchives($planId, $archives);
            if ($archiveResult != true) {
                throw new Exception($archiveResult);
            }

            // 计划关联巡检项
            $planFieldModel = new PlanField();
            $fieldResult = $planFieldModel->addFields($planId, $fields);
            if ($fieldResult != true) {
                throw new Exception($fieldResult);
            }

            // 计划关联巡检人员
            $planUserModel = new PlanUser();
            $userResult = $planUserModel->addUsers($planId, $users);
            if ($userResult != true) {
                throw new Exception($userResult);
            }

            // 计划关联周期任务
            $taskResult = $planTaskModel->addTasks($planId, $archives, $data['periodicity'], $firstDuetime, $lastDuetime);
            if ($taskResult != true) {
                throw new Exception($taskResult);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public function stopPlan($ids)
    {
        Db::startTrans();
        try {
            $result = $this->destroy($ids);
            if (!$result) {
                throw new Exception($this->getError());
            }

            // 计划关联周期任务
            $planTaskModel = new PlanTask();
            $taskIds = $planTaskModel->whereIn('plan_id', $ids)->where(['status' => 'pending'])->column('id');
            if (count($taskIds) > 0) {
                $taskResult = $planTaskModel->destroy($taskIds);
                if ($taskResult != true) {
                    throw new Exception($planTaskModel->getError());
                }
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }
}

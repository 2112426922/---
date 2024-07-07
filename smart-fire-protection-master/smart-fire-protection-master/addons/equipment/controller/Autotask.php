<?php

namespace addons\equipment\controller;

use app\admin\model\equipment\Equipment;
use app\admin\model\equipment\Plan;
use app\admin\model\equipment\PlanTask;
use app\admin\model\equipment\Record;
use think\Db;
use think\Exception;

/**
 * 定时任务
 */
class Autotask extends \think\addons\Controller
{
    protected $noNeedLogin = ["*"];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();

        if (!$this->request->isCli()) {
            $this->error('只允许在终端进行操作!');
        }
    }

    /**
     * 定时任务清理逾期计划任务
     */
    public function index()
    {
        $planTaskModel = new PlanTask();
        $taskList = $planTaskModel->where('status', 'pending')->whereTime('duetime', '<=', time())->column('id, plan_id, equipment_id');
        if (empty($taskList)) {
            echo "none";
            return;
        }

        // 过滤已报废或已删除设备
        $equipmentIds = array_unique(array_column($taskList, 'equipment_id'));
        $delIds = Equipment::onlyTrashed()->whereIn('id', $equipmentIds)->column('id');
        $scrappedIds = Equipment::where(['work_status' => 'scrapped'])->whereIn('id', $equipmentIds)->column('id');
        $filterIds = array_merge($delIds, $scrappedIds);

        $planIds = array_unique(array_column($taskList, 'plan_id'));
        $planList = Plan::where('id', 'in', $planIds)->column('id, type');
        if (empty($planList)) {
            echo "none";
            return;
        }

        $records = [];
        $delPlanIds = [];
        $dueTaskIds = [];
        $time = time();
        $nameArr = ['inspection' => '巡检', 'maintenance' => '保养'];
        foreach ($taskList as $taskId => $item) {
            $equipmentId = $item['equipment_id'];
            if (in_array($equipmentId, $filterIds)) {
                continue;
            }

            $planId = $item['plan_id'];
            if (!isset($planList[$planId])) {
                $delPlanIds[] = $planId;
                continue;
            }

            $dueTaskIds[] = $taskId;
            $type = $planList[$planId];
            $records[] = [
                'equipment_id' => $equipmentId,
                'relate_id' => $taskId,
                'type' => $type,
                'name' => $nameArr[$type] . '结果：已逾期',
                'content' => '[]',
                'createtime' => $time,
                'updatetime' => $time
            ];
        }

        Db::startTrans();
        try {
            $recordModel = new Record();
            $recordResult = $recordModel->insertAll($records);
            if (!$recordResult) {
                throw new Exception($recordModel->getError());
            }

            $result = $planTaskModel->whereIn('id', $dueTaskIds)->update(['status' => 'overdue', 'updatetime' => $time]);
            if (!$result) {
                throw new Exception($planTaskModel->getError());
            }

            if (!empty($filterIds)) {
                $delResult = $planTaskModel->whereIn('equipment_id', $filterIds)->where('status', 'pending')->update(['updatetime' => $time, 'deletetime' => $time]);
                if (!$delResult) {
                    throw new Exception($planTaskModel->getError());
                }
            }

            if (!empty($delPlanIds)) {
                $delResult = $planTaskModel->whereIn('plan_id', array_unique($delPlanIds))->where('status', 'pending')->update(['updatetime' => $time, 'deletetime' => $time]);
                if (!$delResult) {
                    throw new Exception($planTaskModel->getError());
                }
            }

            Db::commit();
            echo "done";
        } catch (Exception $exception) {
            Db::rollback();
            echo $exception->getMessage();
        }

        return;
    }
}
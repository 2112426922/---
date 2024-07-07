<?php

namespace app\admin\controller\equipment;

use app\common\controller\Backend;

/**
 * 看板中心
 *
 * @icon fa fa-circle-o
 */
class Dashboard extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 查看
     */
    public function index()
    {
        $archiveModel = new \app\admin\model\equipment\Archive();
        $equipmentModel = new \app\admin\model\equipment\Equipment();
        $planModel = new \app\admin\model\equipment\Plan();
        $planTaskModel = new \app\admin\model\equipment\PlanTask();
        $repairModel = new \app\admin\model\equipment\Repair();
        $archiveCount = $archiveModel->count();
        $equipmentCount = $equipmentModel->count();
        $equipmentNormalCount = $equipmentModel->where(['work_status' => 'normal'])->count();
        $equipmentSicknessCount = $equipmentModel->where(['work_status' => 'sickness'])->count();
        $equipmentRepairingCount = $equipmentModel->where(['work_status' => 'repairing'])->count();
        $equipmentScrappedCount = $equipmentModel->where(['work_status' => 'scrapped'])->count();
        $inspectionCount = $planModel->where(['type' => 'inspection'])->count();
        $maintenanceCount = $planModel->where(['type' => 'maintenance'])->count();
        // 今日巡检/保养
        $todayBegin = date('Y-m-d 00:00:00', time());
        $todayEnd = date('Y-m-d 23:59:59', time());
        $inspectionTodayIds = $planModel->where(['type' => 'inspection'])->whereTime('last_duetime', '>=', $todayEnd)->column('id');
        $maintenanceTodayIds = $planModel->where(['type' => 'maintenance'])->whereTime('last_duetime', '>=', $todayEnd)->column('id');
        $inspectionTodayPending = $planTaskModel->whereIn('plan_id', $inspectionTodayIds)->where(['status' => 'pending'])->whereTime('starttime', '<=', $todayBegin)->whereTime('duetime', '>=', $todayEnd)->count();
        $inspectionTodayFinish = $planTaskModel->whereIn('plan_id', $inspectionTodayIds)->where(['status' => 'finish'])->whereTime('updatetime', 'today')->count();
        $maintenanceTodayPending = $planTaskModel->whereIn('plan_id', $maintenanceTodayIds)->where(['status' => 'pending'])->whereTime('starttime', '<=', $todayBegin)->whereTime('duetime', '>=', $todayEnd)->count();
        $maintenanceTodayFinish = $planTaskModel->whereIn('plan_id', $maintenanceTodayIds)->where(['status' => 'finish'])->whereTime('updatetime', 'today')->count();
        // 维修工单
        $repairRegisteredCount = $repairModel->where(['status' => 'registered'])->count();
        $repairMonthCount = $repairModel->where(['status' => 'registered'])->whereTime('createtime', 'month')->count();

        // 近7天统计数据
        $days = [];
        $showDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = date('Y-m-d', strtotime(' -' . $i . 'day'));
            $showDays[] = date('m/d', strtotime(' -' . $i . 'day'));
        }
        $repairWeekCreate = [];
        $inspectionWeekFinish = [];
        $maintenanceWeekFinish = [];
        $inspectionWeekIds = $planModel->where(['type' => 'inspection'])->whereTime('last_duetime', '>=', $todayEnd)->column('id');
        $maintenanceWeekIds = $planModel->where(['type' => 'maintenance'])->whereTime('last_duetime', '>=', $todayEnd)->column('id');
        foreach ($days as $day) {
            $beginTime = $day . ' 00:00:00';
            $endTime = $day . ' 23:59:59';
            $repairWeekCreate[$day] = $repairModel->whereTime('createtime', 'between', [$beginTime, $endTime])->count();
            $inspectionWeekFinish[$day] = $planTaskModel->whereIn('plan_id', $inspectionWeekIds)->where(['status' => 'finish'])->whereTime('updatetime', 'between', [$beginTime, $endTime])->count();
            $maintenanceWeekFinish[$day] = $planTaskModel->whereIn('plan_id', $maintenanceWeekIds)->where(['status' => 'finish'])->whereTime('updatetime', 'between', [$beginTime, $endTime])->count();
        }

        // 本月故障原因统计
        $failureCauseCount = $repairModel->whereIn('status', ['repaired', 'scrapped'])->whereTime('createtime', 'month')->group('failure_cause_id')->column('failure_cause_id, count(*)');
        $failureCauseModel = new \app\admin\model\equipment\FailureCause();
        $failureCauseList = $failureCauseModel->whereIn('id', array_keys($failureCauseCount))->column('id, name');
        $failureCause = [];
        $failureCauseNames = [];
        foreach ($failureCauseCount as $failureCauseId => $count) {
            if ($failureCauseId == 0) continue;

            $failureCauseName = $failureCauseList[$failureCauseId];
            $failureCauseNames[] = $failureCauseName;
            $failureCause[] = [
                'value' => $count,
                'name' => $failureCauseName
            ];
        }

        $statisticChart = [
            'weekDate' => $showDays,
            'repairWeekData' => array_values($repairWeekCreate),
            'inspectionWeekData' => array_values($inspectionWeekFinish),
            'maintenanceWeekData' => array_values($maintenanceWeekFinish),
            'failureCauseNames' => $failureCauseNames,
            'failureCause' => $failureCause,
        ];

        $statisticCount = [
            'archiveCount' => $archiveCount,
            'equipmentCount' => $equipmentCount,
            'equipmentNormalCount' => $equipmentNormalCount,
            'equipmentSicknessCount' => $equipmentSicknessCount,
            'equipmentRepairingCount' => $equipmentRepairingCount,
            'equipmentScrappedCount' => $equipmentScrappedCount,
            'inspectionCount' => $inspectionCount,
            'maintenanceCount' => $maintenanceCount,
            'inspectionTodayPending' => $inspectionTodayPending,
            'inspectionTodayFinish' => $inspectionTodayFinish,
            'maintenanceTodayPending' => $maintenanceTodayPending,
            'maintenanceTodayFinish' => $maintenanceTodayFinish,
            'repairRegisteredCount' => $repairRegisteredCount,
            'repairMonthCount' => $repairMonthCount,
        ];

        $this->view->assign('statisticCount', $statisticCount);
        $this->assignconfig('statisticChart', $statisticChart);
        return $this->view->fetch();
    }
}

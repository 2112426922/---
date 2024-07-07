<?php

namespace app\api\controller\equipment;

use app\admin\model\equipment\Archive;
use app\admin\model\equipment\Equipment;
use app\admin\model\equipment\FailureCause;
use app\admin\model\equipment\Plan;
use app\admin\model\equipment\PlanTask;
use app\admin\model\equipment\PlanUser;
use app\admin\model\equipment\Repair;

class Manage extends Base
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        $this->token = $this->request->post('token');
        parent::_initialize();
    }

    public function workbench()
    {
        $user = $this->auth->getUser();
        $userId = $user['id'];

        // 维修工单
        $repair = Repair::where(['repair.status' => 'registered', 'repair_uid' => $userId])->with(['equipment' => 'archive'])->count();
        $repairPool = Repair::where(['repair.status' => 'pending'])->with(['equipment' => 'archive'])->count();

        $nowTime = time();
        // 巡检计划
        $inspectionPlanIds = PlanUser::hasWhere('plan', ['type' => 'inspection', 'last_duetime' => ['>=', $nowTime], 'deletetime' => null])->where('user_id', $userId)->column('plan_id');
        $inspection = PlanTask::where(['plan_task.status' => 'pending', 'eagerlyEquipment.work_status' => ['<>', 'scrapped']])->whereIn('plan_id', $inspectionPlanIds)->whereTime('starttime', '<=', $nowTime)->whereTime('duetime', '>=', $nowTime)->with(['eagerlyEquipment'])->count();

        // 保养计划
        $maintenancePlanIds = PlanUser::hasWhere('plan', ['type' => 'maintenance', 'last_duetime' => ['>=', $nowTime], 'deletetime' => null])->where('user_id', $userId)->column('plan_id');
        $maintenance = PlanTask::where(['plan_task.status' => 'pending', 'eagerlyEquipment.work_status' => ['<>', 'scrapped']])->whereIn('plan_id', $maintenancePlanIds)->whereTime('starttime', '<=', $nowTime)->whereTime('duetime', '>=', $nowTime)->with(['eagerlyEquipment'])->count();

        // 设备运行状态
        $equipmentNormalCount = Equipment::where(['work_status' => 'normal'])->count();
        $equipmentRepairingCount = Equipment::where(['work_status' => 'repairing'])->count();
        $equipmentScrappedCount = Equipment::where(['work_status' => 'scrapped'])->count();

        $todayEnd = date('Y-m-d 23:59:59', $nowTime);
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
        $inspectionWeekIds = Plan::where(['type' => 'inspection'])->whereTime('last_duetime', '>=', $todayEnd)->column('id');
        $maintenanceWeekIds = Plan::where(['type' => 'maintenance'])->whereTime('last_duetime', '>=', $todayEnd)->column('id');
        foreach ($days as $day) {
            $beginTime = $day . ' 00:00:00';
            $endTime = $day . ' 23:59:59';
            $repairWeekCreate[$day] = Repair::whereTime('createtime', 'between', [$beginTime, $endTime])->count();
            $inspectionWeekFinish[$day] = PlanTask::whereIn('plan_id', $inspectionWeekIds)->where(['status' => 'finish'])->whereTime('updatetime', 'between', [$beginTime, $endTime])->count();
            $maintenanceWeekFinish[$day] = PlanTask::whereIn('plan_id', $maintenanceWeekIds)->where(['status' => 'finish'])->whereTime('updatetime', 'between', [$beginTime, $endTime])->count();
        }

        // 本月故障原因统计
        $failureCauseCount = Repair::whereIn('status', ['repaired', 'scrapped'])->whereTime('createtime', 'month')->group('failure_cause_id')->column('failure_cause_id, count(*)');
        $failureCauseList = FailureCause::whereIn('id', array_keys($failureCauseCount))->column('id, name');
        $failureCause = [];
        foreach ($failureCauseCount as $failureCauseId => $count) {
            if ($failureCauseId == 0) continue;

            $failureCause[] = [
                'value' => $count,
                'name' => $failureCauseList[$failureCauseId]
            ];
        }

        $statisticChart = [
            'equipmentStatus' => [
                ['name' => '正常运行 ' . $equipmentNormalCount . '台', 'value' => $equipmentNormalCount],
                ['name' => '停机待修 ' . $equipmentRepairingCount . '台', 'value' => $equipmentRepairingCount],
                ['name' => '报废停用 ' . $equipmentScrappedCount . '台', 'value' => $equipmentScrappedCount],
            ],
            'weekDate' => $showDays,
            'repairWeekData' => array_values($repairWeekCreate),
            'inspectionWeekData' => array_values($inspectionWeekFinish),
            'maintenanceWeekData' => array_values($maintenanceWeekFinish),
            'failureCause' => $failureCause
        ];

        $result = [
            'repair' => $repair,
            'repairPool' => $repairPool,
            'inspection' => $inspection,
            'maintenance' => $maintenance,
            'statisticChart' => $statisticChart
        ];
        $this->success('', $result);
    }

    /**
     * 设备档案列表
     * @return array
     */
    public function archives()
    {
        $page = $this->request->param('page', 1);
        $pageSize = $this->request->param('pageSize', 10);

        $totalCount = Archive::where(['status' => 'normal'])->count();
        if ($totalCount == 0) {
            $result = [
                'list' => [],
                'count' => 0,
                'total_count' => 0,
                'current_page' => 1,
                'total_page' => 1
            ];

            $this->success('', $result);
        }

        $archives = Archive::where(['archive.status' => 'normal'])->with(['supplier'])->page($page, $pageSize)->select();

        $list = [];
        foreach ($archives as $archive) {
            $list[] = [
                'id' => $archive['id'],
                'name' => $archive['name'],
                'model' => $archive['model'],
                'amount' => $archive['amount'],
                'region' => $archive['region'],
                'supplier' => $archive['supplier']['name'],
            ];
        }

        $result = [
            'list' => $list,
            'count' => count($list),
            'total_count' => $totalCount,
            'current_page' => $page,
            'total_page' => ceil($totalCount / $pageSize)
        ];

        $this->success('', $result);
    }

    /**
     * 设备列表
     * @return array
     */
    public function equipments()
    {
        $status = $this->request->post('status', '');
        $archiveKeyword = $this->request->post('archive_keyword', '');
        $equipmentKeyword = $this->request->post('equipment_keyword', '');
        $planType = $this->request->post('plan_type', '');
        $archiveId = $this->request->post('archive_id', '');
        $page = $this->request->post('page', 1);
        $pageSize = $this->request->post('pageSize', 10);

        if ($status == 'scrapped') {
            $result = $this->scrapped($page, $pageSize);
            $this->success('', $result);
        }

        $equipmentWhere = ['status' => 'normal'];
        if (!empty($planType)) {
            $user = $this->auth->getUser();
            $userId = $user['id'];

            $equipmentIds = [];
            // 巡检计划
            if ($planType == 'inspection') {
                $inspectionPlanIds = PlanUser::hasWhere('plan', ['type' => 'inspection', 'last_duetime' => ['>=', time()], 'deletetime' => null])->where('user_id', $userId)->column('plan_id');
                $equipmentIds = PlanTask::where(['status' => 'pending'])->whereIn('plan_id', $inspectionPlanIds)->whereTime('starttime', '<=', time())->whereTime('duetime', '>=', time())->order('duetime asc')->column('equipment_id');
            }
            // 保养计划
            if ($planType == 'maintenance') {
                $maintenancePlanIds = PlanUser::hasWhere('plan', ['type' => 'maintenance', 'last_duetime' => ['>=', time()], 'deletetime' => null])->where('user_id', $userId)->column('plan_id');
                $equipmentIds = PlanTask::where(['status' => 'pending'])->whereIn('plan_id', $maintenancePlanIds)->whereTime('starttime', '<=', time())->whereTime('duetime', '>=', time())->order('duetime asc')->column('equipment_id');
            }

            $equipmentWhere['id'] = ['in', $equipmentIds];
        }

        $archiveWhere = [];
        if (!empty($archiveId)) {
            $archiveWhere['id'] = $archiveId;
        }
        if (!empty($archiveKeyword)) {
            $archiveWhere['model|name'] = ['like', '%' . $archiveKeyword . '%'];
        }
        if (!empty($archiveWhere)) {
            $archiveWhere['status'] = 'normal';
            $archiveIds = Archive::where($archiveWhere)->column('id');
            $equipmentWhere['archive_id'] = ['in', $archiveIds];
        }
        if (!empty($equipmentKeyword)) {
            $equipmentWhere['equipment_code'] = ['like', '%' . $equipmentKeyword . '%'];
        }

        $totalCount = Equipment::where($equipmentWhere)->whereIn('work_status', ['normal', 'repairing'])->count();
        if ($totalCount == 0) {
            $result = [
                'list' => [],
                'count' => 0,
                'total_count' => 0,
                'current_page' => 1,
                'total_page' => 1
            ];

            $this->success('', $result);
        }

        $list = [];
        $equipments = Equipment::where($equipmentWhere)->whereIn('work_status', ['normal', 'repairing'])->page($page, $pageSize)->field('id,archive_id,coding,equipment_code,work_status')->select();
        $archiveIds = array_unique(array_column((array)$equipments, 'archive_id'));
        $archives = Archive::whereIn('id', $archiveIds)->column('*');
        foreach ($equipments as $equipment) {
            $archiveId = $equipment['archive_id'];
            $archive = $archives[$archiveId];
            $list[] = [
                'archive_id' => $archiveId,
                'name' => $archive['name'],
                'model' => $archive['model'],
                'region' => $archive['region'],
                'equipment_id' => $equipment['id'],
                'coding' => $equipment['coding'],
                'equipment_code' => $equipment['equipment_code'],
                'work_status' => $equipment['work_status'],
                'work_status_text' => $equipment['work_status_text'],
            ];
        }

        $result = [
            'list' => $list,
            'count' => count($list),
            'total_count' => $totalCount,
            'current_page' => $page,
            'total_page' => ceil($totalCount / $pageSize)
        ];

        $this->success('', $result);
    }

    /**
     * 停机报废设备列表
     * @return array
     */
    protected function scrapped($page, $pageSize)
    {
        $where = ['work_status' => 'scrapped', 'status' => 'normal'];
        $totalCount = Equipment::where($where)->count();
        if ($totalCount == 0) {
            return [
                'list' => [],
                'count' => 0,
                'total_count' => 0,
                'current_page' => 1,
                'total_page' => 1
            ];
        }

        $list = [];
        $equipments = Equipment::where($where)->with(['archive'])->order('updatetime desc')->page($page, $pageSize)->select();
        foreach ($equipments as $equipment) {
            $archive = $equipment['archive'];
            $list[] = [
                'archive_id' => $archive['id'],
                'name' => $archive['name'],
                'model' => $archive['model'],
                'region' => $archive['region'],
                'equipment_id' => $equipment['id'],
                'coding' => $equipment['coding'],
                'equipment_code' => $equipment['equipment_code'],
                'work_status' => $equipment['work_status'],
                'work_status_text' => $equipment['work_status_text'],
            ];
        }

        return [
            'list' => $list,
            'count' => count($list),
            'total_count' => $totalCount,
            'current_page' => $page,
            'total_page' => ceil($totalCount / $pageSize)
        ];
    }

    /**
     * 维修工单列表
     * @return array
     */
    public function repairs()
    {
        $type = $this->request->param('type', 'registered');
        $page = $this->request->param('page', 1);
        $pageSize = $this->request->param('pageSize', 10);

        $user = $this->auth->getUser();
        $userId = $user['id'];

        $where = [];
        $order = 'registertime asc';
        if ($type == 'pending') {
            $where = ['status' => 'pending'];
        }
        if ($type == 'registered') {
            $where = ['status' => 'registered', 'repair_uid' => $userId];
        }
        if ($type == 'finish') {
            $order = 'repairtime desc';
            $where = ['status' => ['in', ['repaired', 'scrapped']], 'repair_uid' => $userId];
        }

        $totalCount = Repair::where($where)->count();
        if ($totalCount == 0) {
            $result = [
                'list' => [],
                'count' => 0,
                'total_count' => 0,
                'current_page' => 1,
                'total_page' => 1
            ];

            $this->success('', $result);
        }

        $repairs = Repair::where($where)->order($order)->page($page, $pageSize)->select();
        $equipmentIds = array_unique(array_column((array)$repairs, 'equipment_id'));
        $equipmentList = Equipment::withTrashed()->whereIn('id', $equipmentIds)->column('id, coding, equipment_code, archive_id');
        $archiveIds = array_unique(array_column($equipmentList, 'archive_id'));
        $archiveList = Archive::withTrashed()->whereIn('id', $archiveIds)->column('id, model, name, region');

        $list = [];
        foreach ($repairs as $repair) {
            $equipmentId = $repair['equipment_id'];
            $equipment = $equipmentList[$equipmentId];
            $archiveId = $equipment['archive_id'];
            $archive = $archiveList[$archiveId];

            $list[] = [
                'id' => $repair['id'],
                'repair_code' => $repair['repair_code'],
                'equipment_id' => $equipmentId,
                'equipment_coding' => $equipment['coding'],
                'equipment_code' => $equipment['equipment_code'],
                'archive_model' => $archive['model'],
                'archive_name' => $archive['name'],
                'archive_region' => $archive['region'],
                'content' => $repair['content'],
                'registertime' => $repair['registertime_text'],
                'status' => $repair['status'],
                'status_text' => $repair['status_text'],
            ];
        }

        $result = [
            'list' => $list,
            'count' => count($list),
            'total_count' => $totalCount,
            'current_page' => $page,
            'total_page' => ceil($totalCount / $pageSize)
        ];

        $this->success('', $result);
    }

    /**
     * 接收维修工单
     * @return array
     */
    public function receiveRepairs()
    {
        $repairId = $this->request->post('repair_id', '');
        if (!$repairId) {
            $this->error(__('Unknown repair record'));
        }

        $repair = Repair::find($repairId);
        if (!$repair) {
            $this->error(__('Unknown repair record'));
        }
        if ($repair['status'] != 'pending') {
            $this->error(__('Not allowed to receive repair'));
        }

        $user = $this->auth->getUser();
        $data = [
            'repair_uid' => $user['id'],
            'assigntime' => time(),
            'status' => 'registered',
        ];
        $repairModel = new Repair();
        $result = $repairModel->isUpdate(true)->save($data, ['id' => $repairId]);
        if (!$result) {
            $this->error($repairModel->getError());
        }

        $this->success(__('Operation completed'));
    }
}
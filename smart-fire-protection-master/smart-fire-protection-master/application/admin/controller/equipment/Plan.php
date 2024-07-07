<?php

namespace app\admin\controller\equipment;

use app\admin\model\equipment\Equipment;
use app\admin\model\equipment\PlanArchive;
use app\admin\model\equipment\PlanTask;
use app\common\controller\Backend;

/**
 * 设备计划
 *
 * @icon fa fa-circle-o
 */
class Plan extends Backend
{

    /**
     * Plan模型对象
     * @var \app\admin\model\equipment\Plan
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\equipment\Plan;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 停用
     */
    public function stop($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }

        $ids = $ids ? $ids : $this->request->post("ids");
        $ids = explode(',', $ids);
        $planIds = $this->model->whereIn('id', $ids)->select();
        $this->modelValidate = true;
        if (!$planIds) {
            $this->error(__('No Results were found'));
        }

        $result = $this->model->stopPlan($ids);
        if ($result === true) {
            $this->success();
        } else {
            $this->error($result);
        }
    }

    /**
     * 设备明细
     */
    public function list($ids = null)
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $planArchiveModel = new PlanArchive();
            $archiveIds = $planArchiveModel->where('plan_id', $ids)->column('archive_id');

            $equipmentModel = new Equipment();
            $list = $equipmentModel
                ->with(['archive' => ['load_supplier', 'load_responsible_user']])
                ->where($where)
                ->whereIn('archive_id', $archiveIds)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 清理无效任务
     */
    public function clearInvalidTask()
    {
        $planTaskIdsOne = [];
        $planTaskIdsTwo = [];
        $planTaskModel = new PlanTask();
        // 根据计划任务
        $planIds = $planTaskModel->where(['status' => 'pending'])->group('plan_id')->column('plan_id');
        $normalPlanIds = $this->model->whereIn('id', $planIds)->where(['status' => 'normal'])->column('id');
        $invalidPlanIds = array_diff($planIds, $normalPlanIds);
        if (!empty($invalidPlanIds)) {
            $planTaskIdsOne = $planTaskModel->whereIn('plan_id', $invalidPlanIds)->where(['status' => 'pending'])->column('id');
        }

        // 根据设备
        $equipmentIds = $planTaskModel->where(['status' => 'pending'])->group('equipment_id')->column('equipment_id');
        $equipmentModel = new Equipment();
        $normalEquipmentIds = $equipmentModel->whereIn('id', $equipmentIds)->column('id');
        $invalidEquipmentIds = array_diff($equipmentIds, $normalEquipmentIds);
        if (!empty($invalidEquipmentIds)) {
            $planTaskIdsTwo = $planTaskModel->whereIn('equipment_id', $invalidEquipmentIds)->where(['status' => 'pending'])->column('id');
        }

        $planTaskIds = array_unique(array_merge($planTaskIdsOne, $planTaskIdsTwo));
        if (count($planTaskIds) > 0) {
            $planTaskModel->destroy($planTaskIds);
        }

        $this->success();
    }
}

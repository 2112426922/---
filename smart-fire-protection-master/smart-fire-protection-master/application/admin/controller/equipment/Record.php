<?php

namespace app\admin\controller\equipment;

use app\admin\model\equipment\Equipment;
use app\admin\model\equipment\PlanTask;
use app\common\controller\Backend;

/**
 * 设备记录管理
 *
 * @icon fa fa-circle-o
 */
class Record extends Backend
{

    /**
     * Record模型对象
     * @var \app\admin\model\equipment\Record
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\equipment\Record;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 记录明细
     */
    public function list($ids = null, $type = '', $archiveId = '', $equipmentId = '')
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $query = $this->model->with(['equipment', 'equipment.archive', 'user'])->where($where);

            if (in_array($type, ['inspection', 'maintenance'])) {
                $planTaskModel = new PlanTask();
                $tempWhere = ['status' => ['in', ['finish', 'overdue']]];
                if (!$equipmentId && !$archiveId) $tempWhere['plan_id'] = $ids;
                $taskIds = $planTaskModel->where($tempWhere)->column('id');
                $query = $query->whereIn('relate_id', $taskIds)->where('type', $type);
            }
            if ($archiveId) {
                $equipmentModel = new Equipment();
                $equipmentIds = $equipmentModel->where('archive_id', $archiveId)->column('id');
                $query = $query->whereIn('equipment_id', $equipmentIds);
            }
            if ($equipmentId) {
                $query = $query->where('equipment_id', $equipmentId);
            }

            $list = $query->order($sort, $order)->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 详情
     */
    public function detail($ids = null)
    {
        $row = $this->model->find($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if (!empty($row['content'])) {
            $content = json_decode($row['content'], true);
        } else {
            $content = [];
        }

        $this->view->assign('content', $content);
        return $this->view->fetch();
    }
}

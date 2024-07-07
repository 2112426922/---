<?php

namespace app\admin\controller\equipment;

use app\admin\model\equipment\Equipment;
use app\common\controller\Backend;
use think\Db;
use think\Exception;

/**
 * 设备维修管理
 *
 * @icon fa fa-circle-o
 */
class Repair extends Backend
{

    /**
     * Repair模型对象
     * @var \app\admin\model\equipment\Repair
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\equipment\Repair;
        $this->view->assign('statusList', $this->model->getStatusList());
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 查看
     */
    public function index()
    {
        $this->relationSearch = true;

        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        // 获取链接地址参数
        $param = $this->request->param();
        $archiveId = isset($param['archive_id']) ? $param['archive_id'] : '';
        $equipmentId = isset($param['equipment_id']) ? $param['equipment_id'] : '';
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $andWhere = [];
            if (!empty($archiveId)) {
                $equipmentIds = Equipment::where('archive_id', $archiveId)->column('id');
                $andWhere['equipment_id'] = ['in', $equipmentIds];
            }
            if (!empty($equipmentId)) {
                $andWhere['equipment_id'] = $equipmentId;
            }


            $list = $this->model
                ->with(['archive', 'equipment', 'registerUser', 'repairUser', 'failureCause'])
                ->where($where)
                ->where($andWhere)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }

        $this->view->assign('tabsList', $this->model->getTabsList());
        return $this->view->fetch();
    }

    /**
     * 维修登记
     */
    public function register($id = null)
    {
        $row = $this->model->get($id);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
                $repairtime = time();
                $params['id'] = $id;
                $params['repair_uid'] = $this->auth->id;
                $params['repairtime'] = $repairtime;
                $params['consuming'] = $repairtime - $row['registertime'];

                Db::startTrans();
                try {
                    $result = $this->model->isUpdate(true)->save($params);
                    if (!$result) {
                        throw new Exception($this->model->getError());
                    }

                    $equipmentId = $this->model->where('id', $id)->value('equipment_id');
                    $workStatus = $params['status'] == 'scrapped' ? 'scrapped' : 'normal';
                    $equipmentModel = new Equipment();
                    $equipmentResult = $equipmentModel->isUpdate(true)->update(['id' => $equipmentId, 'work_status' => $workStatus]);
                    if (!$equipmentResult) {
                        throw new Exception($equipmentModel->getError());
                    }

                    Db::commit();
                    $this->success();
                } catch (Exception $exception) {
                    Db::rollback();
                    $this->error($exception->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $failureCauseModel = new \app\admin\model\equipment\FailureCause();
        $failureCauseList = $failureCauseModel->getSelectpicker();

        $statusList = $this->model->getStatusList();
        unset($statusList['pending'], $statusList['registered']);

        $this->view->assign('statusList', build_select('row[status]', $statusList, '', ['id' => 'c-status', 'class' => 'form-control selectpicker']));
        $this->view->assign('failureCauseList', build_select('row[failure_cause_id]', $failureCauseList, '', ['id' => 'c-failure-cause', 'class' => 'form-control selectpicker']));
        return $this->view->fetch();
    }

    /**
     * 详情
     */
    public function detail($id = null)
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $row = $this->model->with(['archive' => ['responsibleUser'], 'equipment', 'registerUser', 'repairUser', 'failureCause'])->find($id);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        $this->view->assign('row', $row);
        return $this->view->fetch();
    }
}

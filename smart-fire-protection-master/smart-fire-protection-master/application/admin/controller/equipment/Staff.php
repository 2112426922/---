<?php

namespace app\admin\controller\equipment;

use app\admin\model\equipment\Department;
use app\common\controller\Backend;
use think\Validate;

/**
 * 员工管理管理
 *
 * @icon fa fa-circle-o
 */
class Staff extends Backend
{

    /**
     * Staff模型对象
     * @var \app\admin\model\equipment\Staff
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\equipment\Staff;
        $this->view->assign("statusList", $this->model->getStatusList());
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
        $departmentId = isset($param['department_id']) ? $param['department_id'] : '';
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $andWhere = [];
            if (!empty($departmentId)) {
                $andWhere['department_id'] = $departmentId;
            }

            $list = $this->model
                ->with(['user', 'department'])
                ->where($where)
                ->where($andWhere)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }

        $this->assignconfig('departmentId', $departmentId);
        return $this->view->fetch();
    }

    /**
     * 回收站
     */
    public function recyclebin()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->onlyTrashed()
                ->with(['user'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 员工单选列表页
     */
    public function picker()
    {
        $this->relationSearch = true;

        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        // 获取链接地址参数
        $param = $this->request->param();
        $parentId = isset($param['parent_id']) ? $param['parent_id'] : '';
        $parentType = isset($param['parent_type']) ? $param['parent_type'] : '';
        $infoText = $parentType == 'repair' ? '仅显示有设备管理权限的部门人员' : '';
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $andWhere = [];
            if ($parentType == 'repair') {
                $andWhere['department.equipment_manage'] = 1;
            }

            $list = $this->model
                ->with(['user', 'department'])
                ->where($where)
                ->where($andWhere)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }

        $this->assignconfig('parent_id', $parentId);
        $this->assignconfig('parent_type', $parentType);
        $this->view->assign('info_text', $infoText);
        return $this->view->fetch();
    }

    /**
     * 员工单选列表页
     */
    public function pickerDeal()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        // 获取链接地址参数
        $param = $this->request->param();

        $staffId = $param['id'] ?: '';
        $userId = $param['user_id'] ?: '';
        $parentId = $param['parent_id'] ?: '';
        $parentType = $param['parent_type'] ?: '';
        if (empty($parentType)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }

        // 维修工单指派
        if ($parentType == 'repair') {
            if (!$userId || !$parentId) {
                $this->error(__('Parameter %s can not be empty', ''));
            }

            $repairModel = new \app\admin\model\equipment\Repair();
            $result = $repairModel->assignStaff($parentId, $userId);
            if ($result === true) {
                $this->success();
            } else {
                $this->error($result);
            }
        }

        // 派单通知人员添加
        if ($parentType == 'assign_remind') {
            if (!$staffId) {
                $this->error(__('Parameter %s can not be empty', ''));
            }

            $reminderUsersModel = new \app\admin\model\equipment\ReminderUsers();
            $where = ['staff_id' => $staffId, 'type' => 'assign_repair'];
            $reminderUser = $reminderUsersModel->where($where)->find();
            if ($reminderUser) {
                $this->error(__('This staff has been added'));
            }

            $result = $reminderUsersModel->isUpdate(false)->data($where)->save();
            if ($result) {
                $this->success();
            } else {
                $this->error($reminderUsersModel->getError());
            }
        }

        // 设备档案选择员工
        if ($parentType == 'archive') {
            $data = [
                'staff_id' => $staffId,
                'user_id' => $userId
            ];
            $this->success('', '', $data);
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
                if (!Validate::is($params['password'], '\S{6,16}')) {
                    $this->error(__("Please input correct password"));
                }
                $result = $this->model->addStaff($params);
                if ($result > 0) {
                    $this->success();
                } else {
                    $this->error($result);
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        // 获取链接地址参数
        $param = $this->request->param();
        $departmentId = !empty($param['department_id']) ? $param['department_id'] : '';

        $departmentModel = new Department();
        $this->view->assign('departmentList', build_select('row[department_id]', $departmentModel->getSelectpicker(), $departmentId, ['class' => 'form-control selectpicker', 'data-rule' => 'required']));
        return parent::add();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            $result = $this->model->editStaff($params, $ids);
            if ($result === true) {
                $this->success();
            } else {
                $this->error($result);
            }
        }

        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        $departmentModel = new Department();
        $this->view->assign('rawData', json_encode($row));
        $this->view->assign('departmentList', build_select('row[department_id]', $departmentModel->getSelectpicker(), $row['department_id'], ['class' => 'form-control selectpicker', 'data-rule' => 'required']));
        return parent::edit($ids);
    }

    public function getPlanSelectpage()
    {
        $list = [];
        $keyword = [];
        if ($this->request->request('keyField')) {
            $this->request->filter(['trim', 'strip_tags', 'htmlspecialchars']);
            $keyword = (array)$this->request->request("q_word/a");
        }

        $result = $this->model->getSelectpicker($keyword, 1);
        foreach ($result as $key => $value) {
            $list[] = [
                'id' => $key,
                'title' => $value
            ];
        }

        return [
            'list' => $list,
            'total' => count($list)
        ];
    }

    public function unbind($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        if (empty($row['openid'])) {
            $this->success();
        }
        $result = $this->model->save(['openid' => ''], ['id' => $ids]);
        if ($result) {
            $this->success();
        } else {
            $this->error($this->model->getError());
        }
    }
}

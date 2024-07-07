<?php

namespace app\admin\controller\equipment;

use app\admin\model\equipment\Staff;
use app\common\controller\Backend;

/**
 * 巡检计划
 *
 * @icon fa fa-circle-o
 */
class Inspection extends Backend
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
     * 查看
     */
    public function index()
    {
        $this->relationSearch = true;

        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['planArchives', 'planFields', 'planUsers'])
                ->where($where)
                ->where('type', 'inspection')
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }

        return $this->view->fetch();
    }

    /**
     * 已停用计划
     */
    public function deactivated()
    {
        $this->relationSearch = true;

        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->onlyTrashed()
                ->with(['planArchives', 'planFields', 'planUsers'])
                ->where($where)
                ->where('type', 'inspection')
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }

        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $result = $this->model->addPlan('inspection', $params);
                if ($result === true) {
                    $this->success();
                } else {
                    $this->error($result);
                }
            }

            $this->error(__('Parameter %s can not be empty', ''));
        }

        return parent::add();
    }
}

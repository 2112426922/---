<?php

namespace app\admin\controller\equipment;

use app\common\controller\Backend;

/**
 * 供应商管理管理
 *
 * @icon fa fa-circle-o
 */
class Supplier extends Backend
{

    /**
     * Supplier模型对象
     * @var \app\admin\model\equipment\Supplier
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\equipment\Supplier;
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
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
        }

        $count = $this->model->withTrashed()->count();
        $this->view->assign('supplierCode', 'GYS-' . str_pad($count + 1, 4, "0", STR_PAD_LEFT));
        $this->view->assign('relationshipList', build_select('row[relationship]', $this->model->getRelationshipList(), '', ['id' => 'c-relationship', 'class' => 'form-control selectpicker']));
        return parent::add();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $row = $this->model->get($ids);
        // $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        $this->view->assign('relationshipList', build_select('row[relationship]', $this->model->getRelationshipList(), $row['relationship'], ['id' => 'c-relationship', 'class' => 'form-control selectpicker']));
        return parent::edit($ids);
    }

    /**
     * 合作关系选项
     */
    public function getRelationshipList()
    {
        $relationshipList = $this->model->getRelationshipList();
        $this->success('', '', $relationshipList);
    }
}

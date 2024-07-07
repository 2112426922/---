<?php

namespace app\admin\controller\equipment;

use app\common\controller\Backend;

/**
 * 设备计划字段管理
 *
 * @icon fa fa-circle-o
 */
class PlanField extends Backend
{

    /**
     * PlanField模型对象
     * @var \app\admin\model\equipment\PlanField
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\equipment\PlanField;
        $this->view->assign("statusList", $this->model->getStatusList());
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
        $requireList = $this->model->getRequireList();
        $typeList = $this->model->getTypeList();
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $this->packageField($params, $typeList);
        }

        unset($typeList['textarea'], $typeList['true-false'], $typeList['image']);
        $this->view->assign('id', time());
        $this->view->assign('options', json_encode(['选项一', '选项二']));
        $this->view->assign('requireList', build_radios('row[require]', $requireList, 0));
        $this->view->assign('typeList', build_select('row[type]', $typeList, '', ['id' => 'field-type', 'class' => 'form-control selectpicker', 'data-rule' => 'required']));
        return parent::add();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $requireList = $this->model->getRequireList();
        $typeList = $this->model->getTypeList();
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $this->packageField($params, $typeList);
        }

        $row = $this->request->get();
        $row['options'] = json_encode(explode('==', $row['options']));
        unset($row['dialog']);
        unset($typeList['textarea'], $typeList['true-false'], $typeList['image']);
        $this->view->assign('row', $row);
        $this->view->assign('requireList', build_radios('row[require]', $requireList, $row['require']));
        $this->view->assign('typeList', build_select('row[type]', $typeList, $row['type'], ['id' => 'field-type', 'class' => 'form-control selectpicker', 'data-rule' => 'required']));
        return $this->view->fetch();
    }

    private function packageField($params, $typeList)
    {
        if ($params) {
            if (empty($params['label'])) {
                $this->error(__('Parameter %s can not be empty', __('Label')));
            }

            switch ($params['type']) {
                case 'text':
                case 'textarea':
                case 'number':
                    $params['options'] = [];
                    break;
                case 'true-false':
                case 'radio':
                case 'multiple':
                    if ($params['options'] == '[]') {
                        $this->error(__('Parameter %s can not be empty', __('Options')));
                    }
                    $params['options'] = array_filter(array_values($params['options']));
                    if (empty($params['options'])) {
                        $this->error(__('Parameter %s can not be empty', __('Options')));
                    }

                    $params['default'] = '';
                    break;
                default:
                    $params['options'] = [];
                    $params['default'] = '';
                    break;
            }

            $params['type_text'] = $typeList[$params['type']];
            $this->success('', '', $params);
        }

        $this->error(__('Parameter %s can not be empty', ''));
    }

    /**
     * 检查项明细
     */
    public function fields($ids = null)
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->where($where)
                ->where('plan_id', $ids)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
}

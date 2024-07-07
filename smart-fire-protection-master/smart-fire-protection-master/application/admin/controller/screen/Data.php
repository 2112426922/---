<?php

namespace app\admin\controller\screen;

use app\common\controller\Backend;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 数据集
 *
 * @icon fa fa-circle-o
 */
class Data extends Backend
{

    /**
     * Data模型对象
     * @var \app\common\model\screen\Data
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\screen\Data;
        $this->view->assign("typeList", $this->model->getTypeList());
    }


    /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;

        if (input('_method') == 'run') {
            foreach ($params as $key => $value) {
                $row->setAttr($key, $value, $params);
            }
            $data = $row->gainData();
            $data =json_encode($data, JSON_UNESCAPED_UNICODE);
            $this->success('', '', $data);
        }

        $result = json_decode($params['result'], true) ?? [];
        $params['key_list'] = json_encode(array_keys($result[0]??[]));

        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }

            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;

        if (input('_method') == 'run') {
            foreach ($params as $key => $value) {
                $this->model->setAttr($key, $value, $params);
            }
            $data = $this->model->gainData();
            $data =json_encode($data, JSON_UNESCAPED_UNICODE);
            $this->success('', '', $data);
        }

        $result = json_decode($params['result'], true) ?? [];
        $params['key_list'] = json_encode(array_keys($result[0]??[]));

        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
            $result = $this->model->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }
}

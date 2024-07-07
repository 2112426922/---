<?php

namespace app\admin\controller\equipment;

use app\common\controller\Backend;
use think\Db;
use think\Exception;
/**
 * 设备状态信息
 *
 * @icon fa fa-circle-o
 */
class Message extends Backend
{

    /**
     * Message模型对象
     * @var \app\admin\model\equipment\Message
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\equipment\Message;

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
        //当前是否为关联查询
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
                    ->with(['equipment','archive' => ['load_supplier', 'load_responsible_user']])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

        /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        $ids = explode(',', $ids);
        $this->modelValidate = true;
        Db::startTrans();
        try {
            $result = $this->model->destroy($ids);
            if (!$result) {
                throw new Exception($this->model->getError());
            }
            Db::commit();
        } catch (Exception $exception) {
            Db::rollback();
            $this->error($exception->getMessage());
        }
    }

}

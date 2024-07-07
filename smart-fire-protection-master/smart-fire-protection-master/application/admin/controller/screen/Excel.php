<?php

namespace app\admin\controller\screen;

use app\common\controller\Backend;

/**
 * Excel报管理
 *
 * @icon fa fa-circle-o
 */
class Excel extends Backend
{

    /**
     * Excel模型对象
     * @var \app\common\model\screen\Excel
     */
    protected $model = null;

    protected $searchFields = ['code', 'title','desc', 'id'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\screen\Excel;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','code','title','desc','status','createtime','updatetime']);
                
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

}
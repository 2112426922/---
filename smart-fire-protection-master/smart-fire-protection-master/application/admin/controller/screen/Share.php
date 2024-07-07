<?php

namespace app\admin\controller\screen;

use app\common\controller\Backend;

/**
 * 分享记录
 *
 * @icon fa fa-circle-o
 */
class Share extends Backend
{

    protected $searchFields = ['remark', 'page.title', 'excel.title'];

    /**
     * Share模型对象
     * @var \app\common\model\screen\Share
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\screen\Share;
        $this->view->assign("statusList", $this->model->getStatusList());
    }


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
                    ->with(['page', 'excel'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->getRelation('page')->visible(['title']);

                if ($row['excel_id'] > 0) {
                    $row['url'] = $this->buildurl('/assets/addons/screen#/el/').$row['code'];
                } else {
                    $row['url'] = $this->buildurl('/assets/addons/screen#/aj/').$row['code'];
                }
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    protected function buildurl($url, $vars = [], $suffix = true, $domain = false)
    {
        $domain = request()->domain();
        $url = url($url, [], $suffix, $domain) . ($vars ? '?' . http_build_query($vars) : '');
        $url = preg_replace("/\/((?!index)[\w]+)\.php\//i", "/", $url);
        return $url;
    }

}

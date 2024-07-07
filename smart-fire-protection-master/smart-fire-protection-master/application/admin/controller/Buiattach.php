<?php

namespace app\admin\controller;

use think\Request;
use app\common\controller\Backend;

/**
 * 附件管理
 */
class Buiattach extends Backend
{

    /**
     * @var \app\common\model\Attachment
     */
    protected $model = null;

    protected $searchFields = 'id,filename,url';
    protected $noNeedRight = ['classify'];

    public function _initialize(){
        parent::_initialize();
		$request = Request::instance();
		$module_url = explode("/",$request->url());
        $this->model = model('Attachment');
        $this->view->assign("mimetypeList", \app\common\model\Attachment::getMimetypeList());
        $this->view->assign("categoryList", \app\common\model\Attachment::getCategoryList());
        $this->assignconfig("categoryList", \app\common\model\Attachment::getCategoryList());
		$module_url = isset($module_url[1]) ? $module_url[1] : $request->module();
        $this->view->assign("domain", sprintf("%s/%s", $request->domain(),$module_url));
    }

    /**
     * 查看
     */
    public function index(){
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
		$multiple = $this->request->request('multiple',0);

        if ($this->request->isAjax()) {
            $mimetypeQuery = [];
            $filter = $this->request->request('filter');
            $filterArr = (array)json_decode($filter, true);
            if (isset($filterArr['category']) && $filterArr['category'] == 'unclassed') {
                $filterArr['category'] = ',unclassed';
                $this->request->get(['filter' => json_encode(array_diff_key($filterArr, ['category' => '']))]);
            }
            if (isset($filterArr['mimetype']) && preg_match("/[]\,|\*]/", $filterArr['mimetype'])) {
                $mimetype = $filterArr['mimetype'];
                $filterArr = array_diff_key($filterArr, ['mimetype' => '']);
                $mimetypeQuery = function ($query) use ($mimetype) {
                    $mimetypeArr = explode(',', $mimetype);
                    foreach ($mimetypeArr as $index => $item) {
                        if (stripos($item, "/*") !== false) {
                            $query->whereOr('mimetype', 'like', str_replace("/*", "/", $item) . '%');
                        } else {
                            $query->whereOr('mimetype', 'like', '%' . $item . '%');
                        }
                    }
                };
            }
            $this->request->get(['filter' => json_encode($filterArr)]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model->where('mimetype', 'like','image/%')->where($where)->order($sort, $order)->paginate($limit);
            $cdnurl = preg_replace("/\/(\w+)\.php$/i", '', $this->request->root());
            foreach ($list as $k => &$v) {
                $v['fullurl'] = ($v['storage'] == 'local' ? $cdnurl : $this->view->config['upload']['cdnurl']) . $v['url'];
            }
            unset($v);
            $result = array("total" => $list->total(), "rows" => $list->items(),"multiple"=>$multiple);
            return json($result);
        }
		$this->view->assign("multiple",$multiple);
        return $this->view->fetch();
    }

	/**
	 * 分类
	 */
	public function category(){
		$getCategoryList = \app\common\model\Attachment::getCategoryList();
		unset($getCategoryList['unclassed']);
		$this->view->assign("categoryListJson", json_encode($getCategoryList,JSON_UNESCAPED_UNICODE ));
		return $this->view->fetch();
	}
}

<?php

namespace app\index\controller;

use think\Request;
use app\common\controller\Frontend;

/**
 * 附件管理
 */
class Buiattach extends Frontend {

    protected $model = null;
    protected $noNeedRight = ['*'];
    protected $layout = '';

    public function _initialize() {
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
    public function index() {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        $multiple = $this->request->request('multiple', 0);

        if ($this->request->isAjax()) {
            $mimetypeQuery = [];
            $where = ['mimetype' => ['like', 'image/%'], 'user_id' => $this->auth->id];
            $category = $this->request->request('category', 'all');
            $filename = $this->request->request('filename', '');
            $where['category'] = ($category == 'unclassed') ? '' : trim($category);
            if ($category == 'all') {
                unset($where['category']);
            }
            if (!empty($filename)) {
                $where['filename'] = ['like', sprintf("%%%s%%", $filename)];
            }
            $list = $this->model->where($where)->order("id desc")->paginate(12);
            $cdnurl = preg_replace("/\/(\w+)\.php$/i", '', $this->request->root());
            foreach ($list as $k => &$v) {
                $v['fullurl'] = ($v['storage'] == 'local' ? $cdnurl : $this->view->config['upload']['cdnurl']) . $v['url'];
            }
            unset($v);
            $result = array("total" => $list->total(), "rows" => $list->items(), "multiple" => $multiple);
            return json($result);
        }
        $this->view->assign("multiple", $multiple);
        return $this->view->fetch();
    }

    /**
     * 分类
     */
    public function category() {
        $getCategoryList = \app\common\model\Attachment::getCategoryList();
        unset($getCategoryList['unclassed']);
        $this->view->assign("categoryListJson", json_encode($getCategoryList, JSON_UNESCAPED_UNICODE));
        return $this->view->fetch();
    }

    /**
     * 归类
     */
    public function classify() {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $category = $this->request->post('category', '');
        $ids = $this->request->post('ids');
        if (!$ids) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        $categoryList = \app\common\model\Attachment::getCategoryList();
        if ($category && !isset($categoryList[$category])) {
            $this->error(__('Category not found'));
        }
        $category = $category == 'unclassed' ? '' : $category;
        \app\common\model\Attachment::where('id', 'in', $ids)->where(['user_id' => $this->auth->id])->update(['category' => $category]);
        $this->success();
    }

}

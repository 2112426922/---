<?php

namespace app\admin\controller\equipment;

use addons\qrcode\library\Service;
use app\admin\model\equipment\Archive;
use app\common\controller\Backend;

/**
 * 设备明细管理
 *
 * @icon fa fa-circle-o
 */
class Equipment extends Backend
{

    /**
     * Equipment模型对象
     * @var \app\admin\model\equipment\Equipment
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\equipment\Equipment;
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
        $archiveId = isset($param['archive_id']) ? $param['archive_id'] : '';
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $andWhere = [];
            if (!empty($archiveId)) {
                $andWhere['archive_id'] = $archiveId;
            }

            $list = $this->model
                ->with(['archive' => ['load_supplier', 'load_responsible_user']])
                ->where($where)
                ->where($andWhere)
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
        $archiveIds = $this->model->whereIn('id', $ids)->column('archive_id');
        $this->modelValidate = true;
        if (!$archiveIds) {
            $this->error(__('No Results were found'));
        }

        $result = $this->model->delEquipment($ids);
        if ($result === true) {
            // 刷新设备档案的数量
            $archiveModel = new Archive();
            $archiveModel->refreshAmount(array_unique($archiveIds));

            $this->success();
        }

        $this->error($result);
    }

    /**
     * 二维码弹窗
     */
    public function qrcode($coding, $equipment_code)
    {
        $qrcode = get_addon_info('qrcode');
        if (!$qrcode || !$qrcode['state']) {
            $this->error("请在后台插件管理安装二维码生成插件");
        }

        $config = get_addon_config('equipment');
        if ($config && $config['shorturl']) {
            $shorturl = $config['shorturl'];
            if (substr($shorturl, -1) != '/') {
                $shorturl .= '/';
            }
        } else {
            $shorturl = '';
        }

        $qrcodeData = $shorturl . $coding;
        $params = [
            'text' => $qrcodeData,
            'size' => 300,
            'padding' => 10,
            'label' => $equipment_code,
            'labelfontsize' => 24,
            'labelalignment' => 'center',
        ];

        $qrCode = Service::qrcode($params);
        $dataUri = $qrCode->writeDataUri();

        $this->view->assign('qrcode', $dataUri);
        $this->view->assign('equipmentCode', $equipment_code);
        return $this->view->fetch();
    }
}

<?php

namespace app\admin\controller\equipment;

use app\admin\model\equipment\Equipment;
use app\admin\model\equipment\Staff;
use app\admin\model\equipment\Supplier;
use app\admin\model\equipment\Message;
use app\admin\model\equipment\Messagelog;
use app\common\controller\Backend;
use app\common\model\Attachment;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use think\Db;
use think\Exception;

/**
 * 设备档案管理
 *
 * @icon fa fa-circle-o
 */
class Archive extends Backend
{

    /**
     * Archive模型对象
     * @var \app\admin\model\equipment\Archive
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\equipment\Archive;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function import()
    {
        $requestFile = $this->request->request('file');
        if (!$requestFile) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $requestFile;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        //实例化reader
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $this->error(__('Unknown data format'));
        }
        if ($ext === 'csv') {
            $file = fopen($filePath, 'r');
            $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
            $fp = fopen($filePath, "w");
            $n = 0;
            while ($line = fgets($file)) {
                $line = rtrim($line, "\n\r\0");
                $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
                if ($encoding != 'utf-8') {
                    $line = mb_convert_encoding($line, 'utf-8', $encoding);
                }
                if ($n == 0 || preg_match('/^".*"$/', $line)) {
                    fwrite($fp, $line . "\n");
                } else {
                    fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
                }
                $n++;
            }
            fclose($file) || fclose($fp);

            $reader = new Reader\Csv();
        } elseif ($ext === 'xls') {
            $reader = new Reader\Xls();
        } else {
            $reader = new Reader\Xlsx();
        }

        //加载文件
        $insert = [];
        try {
            if (!$PHPExcel = $reader->load($filePath)) {
                $this->error(__('Unknown data format'));
            }
            $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
            $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
            for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                $row = [];
                for ($currentColumn = 1; $currentColumn <= 9; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $row[] = is_null($val) ? '' : $val;
                }

                // 解析excel日期格式
                $toTimestamp = Date::excelToTimestamp($row[5]);
                $purchasetime = date("Y年m月d日", $toTimestamp);
                $insert[] = ['model' => trim($row[0]), 'name' => trim($row[1]), 'parameter' => trim($row[2]), 'amount' => trim($row[3]), 'supplier_code' => trim($row[4]), 'purchasetime' => $purchasetime, 'region' => trim($row[6]), 'responsible_name' => trim($row[7]), 'responsible_mobile' => trim($row[8])];
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
        if (empty($insert)) {
            $this->error(__('No rows were updated'));
        }

        // 设备型号是否已存在
        $models = array_unique(array_column($insert, 'model'));
        $issetModels = $this->model->whereIn('model', $models)->where('status', 'normal')->column('model');
        if ($issetModels) {
            $this->error('设备型号：' . implode('、', array_unique($issetModels)) . ' 已存在');
        }

        // 供应商编号是否有效
        $supplierCodes = array_filter(array_unique(array_column($insert, 'supplier_code')));
        $supplierModel = new Supplier();
        $supplierIds = $supplierModel->whereIn('supplier_code', $supplierCodes)->where('status', 'normal')->column('id, supplier_code');
        $supplierDiff = array_unique(array_diff($supplierCodes, $supplierIds));
        if (count($supplierDiff) > 0) {
            $this->error('供应商编号：' . implode('、', $supplierDiff) . ' 不存在');
        }

        Db::startTrans();
        try {
            // 新增不存在的员工
            $responsibleUsers = array_column($insert, 'responsible_name', 'responsible_mobile');
            $responsibleMobiles = array_unique(array_column($insert, 'responsible_mobile'));
            $staffModel = new Staff();
            $userIds = $staffModel->with('user')->whereIn('user.mobile', $responsibleMobiles)->where('staff.status', 'normal')->column('user.id, mobile');
            $responsibleDiff = array_unique(array_diff($responsibleMobiles, $userIds));
            if (count($responsibleDiff) > 0) {
                foreach ($responsibleDiff as $mobile) {
                    $data = [
                        'nickname' => $responsibleUsers[$mobile],
                        'mobile' => $mobile,
                        'password' => $mobile
                    ];
                    $staffResult = $staffModel->addStaff($data);
                    if ($staffResult > 0) {
                        $userIds[$staffResult] = $mobile;
                    } else {
                        throw new Exception('手机号码：' . $mobile . ' 员工添加失败。' . __($staffResult));
                    }
                }
            }

            $userIds = array_flip($userIds);
            $supplierIds = array_flip($supplierIds);
            foreach ($insert as $key => $params) {
                $params['supplier_id'] = !empty($params['supplier_code']) ? $supplierIds[$params['supplier_code']] : 0;
                $params['responsible_uid'] = $userIds[$params['responsible_mobile']];
                unset($params['supplier_code'], $params['responsible_name'], $params['responsible_mobile']);

                $result = $this->model->addArchive($params);
                if ($result !== true) {
                    throw new Exception($params['model'] . '：' . $result);
                }
            }

            Db::commit();
            // 删除文件
            Attachment::destroy(['url' => $requestFile]);
            unlink($filePath);
            $this->success();
        } catch (Exception $exception) {
            Db::rollback();
            $this->error($exception->getMessage());
        }
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
                ->with(['supplier', 'responsible_user'])
                ->where($where)
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
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
                if (!($params['amount'] > 0)) {
                    $this->error(__("Number must be greater than 0"));
                }
                $result = $this->model->addArchive($params);
                if ($result === true) {
                    $this->success();
                } else {
                    $this->error($result);
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $staffModel = new Staff;
        $staffList = $staffModel->getSelectpicker();

        $supplierModel = new Supplier;
        $supplierList = $supplierModel->getSelectpicker();
        $supplierList[0] = __('Please select Supplier_id');
        ksort($supplierList);

        $this->assignconfig('staffList', $staffList);
        $this->view->assign('supplierList', build_select('row[supplier_id]', $supplierList, '', ['class' => 'form-control selectpicker', 'data-rule' => 'required']));
        return parent::add();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            $result = $this->model->editArchive($params, $ids);
            if ($result === true) {
                $this->success();
            } else {
                $this->error($result);
            }
        }
        $row = $this->model->get($ids);
        // $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        $staffModel = new Staff;
        $staffList = $staffModel->getSelectpicker();

        $supplierModel = new Supplier;
        $supplierList[0] = '请选择供应商';
        $supplierList = $supplierModel->getSelectpicker();
        ksort($supplierList);

        $this->assignconfig('staffList', $staffList);
        $this->view->assign('responsibleName', $staffList[$row['responsible_uid']]);
        $this->view->assign('supplierList', build_select('row[supplier_id]', $supplierList, $row['supplier_id'], ['class' => 'form-control selectpicker', 'data-rule' => 'required']));
        return parent::edit($ids);
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
        $row = $this->model->get($ids);
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        Db::startTrans();
        try {
            $result = $this->model->destroy($ids);
            if (!$result) {
                throw new Exception($this->model->getError());
            }

            // 删除设备
            $equipmentModel = new Equipment();
            $equipmentIds = $equipmentModel->whereIn('archive_id', $ids)->column('id');
            if (count($equipmentIds) > 0) {
                $equipmentResult = $equipmentModel->delEquipment($equipmentIds);
                if ($equipmentResult !== true) {
                    throw new Exception($equipmentResult);
                }
            }

            // 删除设备相关信息记录
            $messageModel = new Message();
            $messageIds = $messageModel->whereIn('archive_id', $ids)->column('id');
            if (count($messageIds) > 0) {
                $messageResult = $messageModel->destroy($messageIds);
                if (!$messageResult) {
                    throw new Exception($messageModel->getError());
                }
            }

            // 删除设备相关信息记录
            $messagelogModel = new Messagelog();
            $messagelogIds = $messagelogModel->whereIn('archive_id', $ids)->column('id');
            if (count($messagelogIds) > 0) {
                $messagelogResult = $messagelogModel->destroy($messagelogIds);
                if (!$messagelogResult) {
                    throw new Exception($messagelogModel->getError());
                }
            }

            Db::commit();
        } catch (Exception $exception) {
            Db::rollback();
            $this->error($exception->getMessage());
        }

        $this->success();
    }

    /**
     * 导出设备标签数据
     */
    public function exportTag($ids = '')
    {
        $ids = $ids ? $ids : $this->request->post('ids');
        $equipmentModel = new Equipment();
        $equipments = $equipmentModel->whereIn('archive_id', $ids)->column('archive_id, coding, equipment_code', 'id');
        if (!$equipments) {
            $this->error(__('No Results were found'));
        }

        $archives = $this->model->whereIn('id', $ids)->column('*', 'id');
        $SupplierModel = new Supplier();
        $suppliers = $SupplierModel->whereIn('id', array_unique(array_column($archives, 'supplier_id')))->column('name', 'id');
        $userModel = new \app\admin\model\User();
        $responsibleUsers = $userModel->whereIn('id', array_unique(array_column($archives, 'responsible_uid')))->column('nickname, mobile', 'id');

        $config = get_addon_config('equipment');
        if ($config && $config['shorturl']) {
            $shorturl = $config['shorturl'];
            if (substr($shorturl, -1) != '/') {
                $shorturl .= '/';
            }
        } else {
            $shorturl = '';
        }

        $number = 1;
        $columns = [
            ['序号', '设备名称', '设备型号', '设备编号', '所在区域', '供应商', '负责人员', '联系电话', '采购时间', '二维码内容']
        ];
        foreach ($equipments as $equipment) {
            $archive = $archives[$equipment['archive_id']];
            $columns[] = [
                $number,
                $archive['name'],
                $archive['model'],
                $equipment['equipment_code'],
                $archive['region'],
                $archive['supplier_id'] > 0 ? $suppliers[$archive['supplier_id']] : '',
                $responsibleUsers[$archive['responsible_uid']]['nickname'],
                $responsibleUsers[$archive['responsible_uid']]['mobile'],
                date('Y年m月d日', $archive['purchasetime']),
                $shorturl . $equipment['coding']
            ];

            $number++;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);
        $worksheet = $spreadsheet->setActiveSheetIndex(0);

        $line = 0;
        foreach ($columns as $column) {
            $line++;
            $col = 0;
            foreach ($column as $value) {
                $worksheet->setCellValueByColumnAndRow($col, $line, $value);
                $col++;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="设备标签数据_' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $objWriter->save('php://output');
        exit;
    }
}

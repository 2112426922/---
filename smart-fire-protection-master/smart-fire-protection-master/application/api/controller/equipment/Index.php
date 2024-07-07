<?php

namespace app\api\controller\equipment;

use app\admin\model\equipment\Archive;
use app\admin\model\equipment\Department;
use app\admin\model\equipment\Equipment;
use app\admin\model\equipment\FailureCause;
use app\admin\model\equipment\Plan;
use app\admin\model\equipment\PlanField;
use app\admin\model\equipment\PlanTask;
use app\admin\model\equipment\PlanUser;
use app\admin\model\equipment\Record;
use app\admin\model\equipment\ReminderUsers;
use app\admin\model\equipment\Repair;
use app\admin\model\equipment\Staff;
use app\admin\model\equipment\Message;
use think\Db;
use think\Exception;

class Index extends Base
{
    protected $noNeedLogin = ['getSystemInfo', 'equipments', 'optimizeRepair','getcodemessageInfo'];
    protected $noNeedRight = ['*'];

    public function getSystemInfo()
    {
        $config = get_addon_config('equipment');
        $result = [
            'manage_phone' => $config['manage_phone']
        ];

        $this->success('', $result);
    }

    public function getcodemessageInfo()
    {
        $code = $this->request->request('code') ?? '';
        if (empty($code)) {
            $this->error(__('Unknown equipment'));
        }

        $info = Equipment::where('equipment_code', $code)->with(['archive' => ['loadResponsibleUser', 'loadSupplier']])->find();
        $message=Message::where('code',$code)->find();
        if (!$info|| ! $message) {
            $this->error(__('Unknown equipment'));
        }
        $data = [
            'id' => $info['id'],
            'coding' => $info['coding'],
            'equipment_code' => $info['equipment_code'],
            'archive_model' => $info['archive']['model'],
            'archive_name' => $info['archive']['name'],
            'region' => $info['archive']['region'],
            'remark' => $info['archive']['remark'],
            'purchasetime' => $info['archive']['purchasetime_text'],
            'supplier' => ($info['archive']['supplier_id'] > 0) ? $info['archive']['load_supplier']['name'] : '',
            'responsible_name' => $info['archive']['load_responsible_user']['nickname'],
            'responsible_mobile' => $info['archive']['load_responsible_user']['mobile'],
            'work_status' => $info['work_status'],
            'work_status_text' => $info['work_status_text'],
            'todos' => [],
            'records' => [],
            'temp'=> $message['temp'],
            'hum'=> $message['hum'],
            'press'=> $message['press'],
            'light'=> $message['light'],
            'sta'=> $message['sta'],
            'floor'=> $message['floor'],
            'x'=> $message['x'],
            'y'=> $message['y'],
        ];
        $this->success('', $data);
    }

    public function optimizeRepair()
    {
        $repairModel = new Repair();
        $list = $repairModel->withTrashed()->order('createtime asc')->column('*');
   
        $days = [];
        $save = [];
        foreach ($list as $id => $item) {
            if (!empty($item['repair_code'])) continue;
            
            $num = 1;
            $codeDate = date('ymd', $item['createtime']);
            if (isset($days[$codeDate])) {
                $num = $days[$codeDate] + 1;
            }
            $days[$codeDate] = $num;
   
            $tmp = [
                'id' => $id,
                'repair_code' => 'R' . $codeDate . '-' . str_pad($num, 3, "0", STR_PAD_LEFT)
            ];
            if ($item['repair_uid'] == 0 && $item['status'] == 'registered') {
                $tmp['status'] = 'pending';
            }
            $save[] = $tmp;
        }
   
        $result = $repairModel->saveAll($save);
        $this->success('', $result);
    }
    
    /**
     * 设备信息
     * @return array
     */
    public function equipments()
    {
        $coding = $this->request->request('coding') ?? '';
        if (empty($coding)) {
            $this->error(__('Unknown equipment'));
        }

        $info = Equipment::where('coding', $coding)->with(['archive' => ['loadResponsibleUser', 'loadSupplier']])->find();
        if (!$info) {
            $this->error(__('Unknown equipment'));
        }
        $message=Message::where('code',$info['equipment_code'])->find();
        $document = trim($info['archive']['document']);
        if (!empty($document) && substr($document, 0, 7) !== "http://" && substr($document, 0, 8) !== "https://") {
            $document = cdnurl($document, true);
        }

        $data = [
            'id' => $info['id'],
            'coding' => $info['coding'],
            'equipment_code' => $info['equipment_code'],
            'archive_model' => $info['archive']['model'],
            'archive_name' => $info['archive']['name'],
            'region' => $info['archive']['region'],
            'remark' => $info['archive']['remark'],
            'document' => $document,
            'purchasetime' => $info['archive']['purchasetime_text'],
            'supplier' => ($info['archive']['supplier_id'] > 0) ? $info['archive']['load_supplier']['name'] : '',
            'responsible_name' => $info['archive']['load_responsible_user']['nickname'],
            'responsible_mobile' => $info['archive']['load_responsible_user']['mobile'],
            'work_status' => $info['work_status'],
            'work_status_text' => $info['work_status_text'],
            'todos' => [],
            'records' => [],
            'temp'=> $message['temp'],
            'hum'=> $message['hum'],
            'press'=> $message['press'],
            'light'=> $message['light'],
            'sta'=> $message['sta'],
        ];

        $user = $this->auth->getUser();
        if (!$user) {
            $this->success('', $data);
        }

        // 设备日志
        $records = [];
        $recordList = Record::where('equipment_id', $info['id'])->with('user')->order('id desc')->select();
        foreach ($recordList as $item) {
            $records[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'user' => ($item['add_uid'] == 0) ? '系统管理员' : $item['user']['nickname'],
                'type' => $item['type'],
                'createtime' => date("Y年m月d日 H:i:s", $item['createtime'])
            ];
        }
        $data['records'] = $records;

        if ($data['work_status'] == 'scrapped') {
            $this->success('', $data);
        }

        $userId = $user['id'];
        // 维修工单
        $repair = Repair::where(['equipment_id' => $info['id'], 'repair_uid' => $userId, 'status' => 'registered'])->find();
        if ($repair) {
            $data['todos']['repair'] = [
                'id' => $repair['id'],
                'title' => $repair['repair_code']
            ];
        }

        // 巡检计划
        $inspectionPlanIds = PlanUser::hasWhere('plan', ['type' => 'inspection', 'last_duetime' => ['>=', time()], 'deletetime' => null])->where('user_id', $userId)->column('plan_id');
        $inspection = PlanTask::where(['equipment_id' => $info['id'], 'status' => 'pending'])->whereIn('plan_id', $inspectionPlanIds)->whereTime('starttime', '<=', time())->whereTime('duetime', '>=', time())->with('plan')->order('duetime asc')->find();
        if ($inspection) {
            $data['todos']['inspection'] = [
                'id' => $inspection['id'],
                'title' => $inspection['plan']['name'],
                'periodicity' => $inspection['plan']['periodicity'],
                'duetime' => date("Y年m月d日 H:i", $inspection['duetime'])
            ];
        }

        // 保养计划
        $maintenancePlanIds = PlanUser::hasWhere('plan', ['type' => 'maintenance', 'last_duetime' => ['>=', time()], 'deletetime' => null])->where('user_id', $userId)->column('plan_id');
        $maintenance = PlanTask::where(['equipment_id' => $info['id'], 'status' => 'pending'])->whereIn('plan_id', $maintenancePlanIds)->whereTime('starttime', '<=', time())->whereTime('duetime', '>=', time())->with('plan')->order('duetime asc')->find();
        if ($maintenance) {
            $data['todos']['maintenance'] = [
                'id' => $maintenance['id'],
                'title' => $maintenance['plan']['name'],
                'periodicity' => $maintenance['plan']['periodicity'],
                'duetime' => date("Y年m月d日 H:i", $maintenance['duetime'])
            ];
        }

        $this->success('', $data);
    }

    /**
     * 我的设备
     * 2.3.0开始弃用
     * @return array
     */
    public function lists()
    {
        $user = $this->auth->getUser();
        $userId = $user['id'];

        $repairing = Archive::where(['responsible_uid' => $userId, 'status' => 'normal'])->with(['equipments' => function ($query) {
            $query->where(['work_status' => 'repairing'])->field('id,archive_id,coding,equipment_code,work_status');
        }])->select();
        $normal = Archive::where(['responsible_uid' => $userId, 'status' => 'normal'])->with(['equipments' => function ($query) {
            $query->where(['work_status' => 'normal'])->field('id,archive_id,coding,equipment_code,work_status');
        }])->select();
        $scrapped = Archive::where(['responsible_uid' => $userId, 'status' => 'normal'])->with(['equipments' => function ($query) {
            $query->where(['work_status' => 'scrapped'])->field('id,archive_id,coding,equipment_code,work_status');
        }])->select();
        $archives = array_merge($repairing, $normal, $scrapped);
        if (!$archives) {
            $this->success('', []);
        }

        $list = [];
        foreach ($archives as $archive) {
            foreach ($archive['equipments'] as $equipment) {
                $list[] = [
                    'archive_id' => $archive['id'],
                    'name' => $archive['name'],
                    'model' => $archive['model'],
                    'region' => $archive['region'],
                    'equipment_id' => $equipment['id'],
                    'coding' => $equipment['coding'],
                    'equipment_code' => $equipment['equipment_code'],
                    'work_status' => $equipment['work_status'],
                    'work_status_text' => $equipment['work_status_text'],
                ];
            }
        }
        $this->success('', $list);
    }

    /**
     * 设备报修
     * @return array
     */
    public function repairs()
    {
        $equipmentId = $this->request->post('equipment_id', '');
        $content = $this->request->post('content', '');
        $registerImage = $this->request->post('register_image', '');
        if (empty($equipmentId)) {
            $this->error(__('Unknown equipment'));
        }

        $repair = Repair::where(['equipment_id' => $equipmentId, 'status' => 'registered'])->find();
        if ($repair) {
            $this->error(__('The device is in repair status and does not need to be resubmitted'));
        }

        $archiveId = Equipment::where(['id' => $equipmentId, 'status' => 'normal'])->value('archive_id');
        if (!$archiveId) {
            $this->error(__('Unknown equipment'));
        }

        $needRemind = true;
        Db::startTrans();
        try {
            $repairModel = new Repair();
            $repairCode = $repairModel->generateCode(1)[0];

            $user = $this->auth->getUser();
            $userId = $user['id'];
            $data = [
                'repair_code' => $repairCode,
                'archive_id' => $archiveId,
                'equipment_id' => $equipmentId,
                'content' => $content,
                'register_image' => $registerImage,
                'register_uid' => $userId,
                'registertime' => time(),
                'status' => 'pending',
            ];

            $equipmentConfig = get_addon_config('equipment');
            if ($equipmentConfig['repair_assign_oneself'] == 1) {
                // 判断是否为设备管理人员，若是则工单默认指派给自己
                $staff = Staff::getByUserId($userId);
                $department = Department::find($staff['department_id']);
                if ($department && $department['equipment_manage'] == 1) {
                    $needRemind = false;
                    $data['repair_uid'] = $userId;
                    $data['assigntime'] = time();
                    $data['status'] = 'registered';
                }
            }

            $result = $repairModel->isUpdate(false)->data($data)->save();
            if (!$result) {
                throw new Exception($repairModel->getError());
            }

            $equipmentModel = new Equipment();
            $equipmentResult = $equipmentModel->isUpdate(true)->save(['work_status' => 'repairing'], ['id' => $equipmentId]);
            if (!$equipmentResult) {
                throw new Exception($equipmentModel->getError());
            }

            Db::commit();
        } catch (Exception $exception) {
            Db::rollback();
            $this->error($exception->getMessage());
        }

        if ($needRemind) {
            $reminderUsersModel = new ReminderUsers();
            $reminderUsersModel->toRemindSMS('newRepair');
        }

        $this->success(__('Operation completed'));
    }

    /**
     * 维修工单详情
     * @return array
     */
    public function repairInfos()
    {
        $id = $this->request->request('id') ?? '';
        if (!$id) {
            $this->error(__('Unknown repair record'));
        }

        $repair = Repair::where('repair.id', $id)->with(['registerUser', 'repairUser'])->find();
        if ($repair) {
            $registerImage = trim($repair['register_image']);
            if (!empty($registerImage) && substr($registerImage, 0, 7) !== "http://" && substr($registerImage, 0, 8) !== "https://") {
                $registerImage = cdnurl($registerImage, true);
            }

            $equipment = Equipment::withTrashed()->where('id', $repair['equipment_id'])->find();
            $archive = Archive::withTrashed()->where('id', $equipment['archive_id'])->find();
            $data = [
                'repair_id' => $id,
                'repair_code' => $repair['repair_code'],
                'equipment_id' => $repair['equipment_id'],
                'equipment_coding' => $equipment['coding'],
                'equipment_code' => $equipment['equipment_code'],
                'archive_model' => $archive['model'],
                'archive_name' => $archive['name'],
                'archive_region' => $archive['region'],
                'register_image' => $registerImage,
                'register_name' => $repair['register_user']['nickname'],
                'register_mobile' => $repair['register_user']['mobile'],
                'register_content' => $repair['content'],
                'registertime' => $repair['registertime_text'],
                'assigntime' => $repair['assigntime'] ? $repair['assigntime_text'] : '',
                'repair_username' => $repair['repair_uid'] ? $repair['repair_user']['nickname'] : '',
                'status' => $repair['status'],
                'status_text' => $repair['status_text'],
            ];

            if (in_array($repair['status'], ['repaired', 'scrapped'])) {
                $repairImage = trim($repair['repair_image']);
                if (!empty($repairImage) && substr($repairImage, 0, 7) !== "http://" && substr($repairImage, 0, 8) !== "https://") {
                    $repairImage = cdnurl($repairImage, true);
                }

                $failureCause = FailureCause::withTrashed()->where('id', $repair['failure_cause_id'])->find();

                $data['repairtime'] = $repair['repairtime_text'];
                $data['failure_cause'] = $failureCause['name'];
                $data['repair_content'] = $repair['repair_content'];
                $data['repair_image'] = $repairImage;
                $data['consuming'] = $repair['consuming_text'];
            }

            $this->success('', $data);
        }

        $this->error(__('Unknown repair record'));
    }

    /**
     * 设备维修登记
     * @return array
     */
    public function registers()
    {
        $repairId = $this->request->post('repair_id', '');
        $repairContent = $this->request->post('repair_content', '');
        $repairImage = $this->request->post('repair_image', '');
        $repairStatus = $this->request->post('repair_status', '');
        $failureCauseId = $this->request->post('failure_cause_id', 0);
        if (!$repairId) {
            $this->error(__('Unknown repair record'));
        }
        if (!in_array($repairStatus, ['normal', 'pending', 'registered', 'repaired', 'scrapped'])) {
            $this->error(__('Invalid parameters'));
        }

        $user = $this->auth->getUser();
        $repair = Repair::find($repairId);
        if (!$repair) {
            $this->error(__('Unknown repair record'));
        }
        if ($repair['status'] != 'registered') {
            $this->error(__('The equipment is not in repair status'));
        }
        if ($repair['repair_uid'] != $user['id']) {
            $this->error(__('The repair does not belong to you'));
        }

        $equipmentId = $repair['equipment_id'];
        $archiveId = Equipment::where(['id' => $equipmentId, 'status' => 'normal'])->value('archive_id');
        if (!$archiveId) {
            $this->error(__('Unknown equipment'));
        }

        Db::startTrans();
        try {
            $repairtime = time();
            $data = [
                'repairtime' => $repairtime,
                'repair_content' => $repairContent,
                'repair_image' => $repairImage,
                'failure_cause_id' => $failureCauseId,
                'consuming' => $repairtime - $repair['registertime'],
                'status' => $repairStatus,
            ];
            $repairModel = new Repair();
            $result = $repairModel->isUpdate(true)->save($data, ['id' => $repairId]);
            if (!$result) {
                throw new Exception($repairModel->getError());
            }

            $equipmentModel = new Equipment();
            $workStatus = $repairStatus == 'scrapped' ? 'scrapped' : 'normal';
            $equipmentResult = $equipmentModel->isUpdate(true)->save(['work_status' => $workStatus], ['id' => $equipmentId]);
            if (!$equipmentResult) {
                throw new Exception($equipmentModel->getError());
            }

            // 添加记录
            $recordData = [
                'equipment_id' => $equipmentId,
                'relate_id' => $repairId,
                'add_uid' => $user['id'],
                'type' => 'repair',
                'name' => '维修结果：' . __(ucfirst($repairStatus)),
                'content' => '[]'
            ];
            $recordModel = new Record();
            $recordResult = $recordModel->save($recordData);
            if (!$recordResult) {
                throw new Exception($recordModel->getError());
            }

            Db::commit();
        } catch (Exception $exception) {
            Db::rollback();
            $this->error($exception->getMessage());
        }

        $this->success(__('Operation completed'));
    }

    /**
     * 获取故障原因
     * @return array
     */
    public function getFailureCause()
    {
        $failureCauseModel = new FailureCause();
        $list = $failureCauseModel->getSelectpicker();

        $result = [];
        foreach ($list as $id => $name) {
            $result[] = [
                'id' => $id,
                'name' => $name
            ];
        }

        $this->success('', $result);
    }

    /**
     * 计划任务字段
     * @return array
     */
    public function planTaskFields()
    {
        $id = $this->request->request('id') ?? '';
        if (!$id) {
            $this->error(__('Unknown plan task'));
        }

        $task = PlanTask::find($id);
        if (!$task) {
            $this->error(__('Unknown plan task'));
        }

        $fields = PlanField::where('plan_id', $task['plan_id'])->select();
        $this->success('', $fields);
    }

    /**
     * 完成计划任务
     * @return array
     */
    public function submitPlanTasks()
    {
        $id = $this->request->post('id', '');
        $type = $this->request->post('type', '');
        $content = $this->request->post('content/a', []);
        if (!$id) {
            $this->error(__('Unknown plan task'));
        }
        $planTaskModel = new PlanTask();
        $task = $planTaskModel->find($id);
        if (!$task) {
            $this->error(__('Unknown plan task'));
        }
        if (!$type) {
            $planModel = new Plan();
            $type = $planModel->where('id', $id)->value('type');
        }

        Db::startTrans();
        try {
            $user = $this->auth->getUser();
            $data = [
                'task_uid' => $user['id'],
                'status' => 'finish'
            ];
            $result = $planTaskModel->isUpdate(true)->save($data, ['id' => $id]);
            if (!$result) {
                throw new Exception($planTaskModel->getError());
            }

            // 添加记录
            $recordData = [
                'equipment_id' => $task['equipment_id'],
                'relate_id' => $id,
                'add_uid' => $user['id'],
                'content' => json_encode($content)
            ];
            if ($type == 'inspection') {
                $recordData['type'] = 'inspection';
                $recordData['name'] = '巡检结果：' . $content['work_status'];
            }
            if ($type == 'maintenance') {
                $recordData['type'] = 'maintenance';
                $recordData['name'] = '保养完成';
            }
            $recordModel = new Record();
            $recordResult = $recordModel->save($recordData);
            if (!$recordResult) {
                throw new Exception($recordModel->getError());
            }

            Db::commit();
        } catch (Exception $exception) {
            Db::rollback();
            $this->error($exception->getMessage());
        }

        $this->success(__('Operation completed'));
    }

    /**
     * 完成计划任务
     * @return array
     */
    public function getRecordInfo()
    {
        $id = $this->request->post('id', '');
        if (!$id) {
            $this->error(__('Unknown record'));
        }

        $info = Record::where('id', $id)->with(['equipment.archive', 'user'])->find();
        if ($info['type'] == 'repair') {
            $repair = Repair::where('id', $info['relate_id'])->find();
            $registerImage = trim($repair['register_image']);
            $repairImage = trim($repair['repair_image']);
            if (!empty($registerImage) && substr($registerImage, 0, 7) !== "http://" && substr($registerImage, 0, 8) !== "https://") {
                $registerImage = cdnurl($registerImage, true);
            }
            if (!empty($repairImage) && substr($repairImage, 0, 7) !== "http://" && substr($repairImage, 0, 8) !== "https://") {
                $repairImage = cdnurl($repairImage, true);
            }
            $result = [
                'id' => $info['id'],
                'equipment_id' => $info['equipment_id'],
                'relate_id' => $info['relate_id'],
                'type' => $info['type'],
                'name' => $info['name'],
                'createtime' => date('Y-m-d H:i:s', $info['createtime']),
                'user' => $info['user']['nickname'],
                'archive_name' => $info['equipment']['archive']['name'],
                'archive_model' => $info['equipment']['archive']['model'],
                'equipment_code' => $info['equipment']['equipment_code'],
                'register_user' => $repair['registerUser']['nickname'],
                'register_content' => $repair['content'],
                'register_image' => $registerImage,
                'registertime' => date('Y-m-d H:i:s', $repair['registertime']),
                'repair_user' => $repair['repairUser']['nickname'],
                'repair_content' => $repair['repair_content'],
                'repair_image' => $repairImage,
                'repairtime' => date('Y-m-d H:i:s', $repair['repairtime']),
                'failure_cause' => $repair['failure_cause_id'] ? $repair['failureCause']['name'] : '',
                'consuming' => $repair['consuming_text'],
                'status_text' => $repair['status_text']
            ];
        } else {
            $result = [
                'id' => $info['id'],
                'equipment_id' => $info['equipment_id'],
                'relate_id' => $info['relate_id'],
                'type' => $info['type'],
                'name' => $info['name'],
                'content' => json_decode($info['content'], true),
                'createtime' => date('Y-m-d H:i:s', $info['createtime']),
                'user' => ($info['add_uid'] > 0) ? $info['user']['nickname'] : '系统管理员',
                'archive_name' => $info['equipment']['archive']['name'],
                'archive_model' => $info['equipment']['archive']['model'],
                'equipment_code' => $info['equipment']['equipment_code'],
            ];
        }

        $this->success('', $result);
    }
}
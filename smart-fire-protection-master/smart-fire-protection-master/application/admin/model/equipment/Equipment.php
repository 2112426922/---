<?php

namespace app\admin\model\equipment;

use think\Db;
use think\Exception;
use think\Model;
use traits\model\SoftDelete;

class Equipment extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_equipment';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'work_status_text',
        'status_text'
    ];

    public function getWorkStatusList()
    {
        return ['normal' => __('Normal'), 'sickness' => __('Sickness'), 'repairing' => __('Repairing'), 'scrapped' => __('Scrapped')];
    }

    public function getWorkStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['work_status']) ? $data['work_status'] : '');
        $list = $this->getWorkStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function archive()
    {
        return $this->belongsTo(Archive::class, 'archive_id');
    }

    public function addEquipment($archiveId, $amount = 1)
    {
        $data = [];
        $data2 = [];
        $codings = $this->createCoding($amount, 'E');
        $equipmentCodes = $this->generateCode($amount);
        foreach ($codings as $key => $coding) {
            $data[] = [
                'archive_id' => $archiveId,
                'coding' => $coding,
                'equipment_code' => $equipmentCodes[$key],
                'status' => 'normal'
            ];
            $data2[] = [
                'archive_id' => $archiveId,
                'code' => $equipmentCodes[$key],
            ];
        }
        $message = new Message();
        $message->saveAll($data2);
        $result = $this->saveAll($data);
        if (!$result) {
            return $this->getError();
        }

        return true;
    }

    public function createCoding($amount, $codingPredix = 'E')
    {
        $codings = [];
        for ($i = 0; $i < $amount; $i++) {
            $codings[] = $codingPredix . $this->_coding();
        }

        switch ($codingPredix) {
            case 'P':
                $model = new Plan();
                break;
            case 'T':
                $model = new PlanTask();
                break;
            default:
                $model = $this;
                break;
        }
        if ($model->where('coding', 'in', $codings)->column('id')) {
            return $this->createCoding($amount, $codingPredix);
        } else {
            return $codings;
        }
    }

    // 生成coding
    private function _coding()
    {
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';    // 26个字符
        return substr(str_shuffle($str), 0, 7);
    }

    private function generateCode($amount)
    {
        $beginNum = $this->withTrashed()->whereTime('createtime', 'today')->count() + 1;

        $generateCodes = [];
        $codeDate = date('ymd');
        for ($i = 0; $i < $amount; $i++) {
            $generateCodes[] = 'E' . $codeDate . '-' . str_pad($beginNum, 3, "0", STR_PAD_LEFT);
            $beginNum++;
        }

        return $generateCodes;
    }

    public function delEquipment($ids)
    {
        $rawData = $this->get($ids);
        Db::startTrans();
        try {
            // 删除设备
            $result = $this->destroy($ids);
            if (!$result) {
                throw new Exception($this->getError());
            }

            // 删除设备相关计划任务
            $planTaskModel = new PlanTask();
            $planTaskIds = $planTaskModel->whereIn('equipment_id', $ids)->column('id');
            if (count($planTaskIds) > 0) {
                $planTaskResult = $planTaskModel->destroy($planTaskIds);
                if (!$planTaskResult) {
                    throw new Exception($planTaskModel->getError());
                }
            }

            // 删除设备相关溯源记录
            $recordModel = new Record();
            $recordIds = $recordModel->whereIn('equipment_id', $ids)->column('id');
            if (count($recordIds) > 0) {
                $recordResult = $recordModel->destroy($recordIds);
                if (!$recordResult) {
                    throw new Exception($recordModel->getError());
                }
            }

            // 删除设备相关信息记录
            $messageModel = new Message();
            $messageIds = $messageModel->where('code', $rawData['equipment_code'])->column('id');
            if (count($messageIds) > 0) {
                $messageResult = $messageModel->destroy($messageIds);
                if (!$messageResult) {
                    throw new Exception($messageModel->getError());
                }
            }

            // 删除设备相关信息记录
            $messagelogModel = new Messagelog();
            $messagelogIds = $messagelogModel->where('code', $rawData['equipment_code'])->column('id');
            if (count($messagelogIds) > 0) {
                $messagelogResult = $messagelogModel->destroy($messagelogIds);
                if (!$messagelogResult) {
                    throw new Exception($messagelogModel->getError());
                }
            }

            Db::commit();
            return true;
        } catch (Exception $exception) {
            Db::rollback();
            return $exception->getMessage();
        }
    }
}

<?php

namespace app\admin\model\equipment;

use think\Db;
use think\Exception;
use think\Model;
use traits\model\SoftDelete;

class Archive extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_archive';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'purchasetime_text',
        'status_text'
    ];


    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getPurchasetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['purchasetime']) ? $data['purchasetime'] : '');
        return is_numeric($value) ? date("Y年m月d日", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setPurchasetimeAttr($value)
    {
        if(!is_numeric($value)) {
            $value = str_replace(['年', '月', '日'], ['-', '-', ''], $value);
        }

        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(\app\admin\model\User::class, 'responsible_uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function loadSupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function loadResponsibleUser()
    {
        return $this->belongsTo(\app\admin\model\User::class, 'responsible_uid');
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'archive_id', 'id');
    }

    public function refreshAmount($ids)
    {
        $archives = $this->whereIn('id', $ids)->with('equipments')->select();

        $update = [];
        foreach ($archives as $archive) {
            $update[] = [
                'id' => $archive['id'],
                'amount' => count($archive['equipments'])
            ];
        }
        $result = $this->isUpdate(true)->saveAll($update);
        if(!$result) return $this->getError();
        return true;
    }

    public function addArchive($data)
    {
        Db::startTrans();
        try {
            $result = $this->isUpdate(false)->data($data,true)->save();
            if (!$result) {
                throw new Exception($this->getError());
            }

            $equipmentModel = new Equipment();
            $equipmentResult = $equipmentModel->addEquipment($this->id, $data['amount']);
            if($equipmentResult != true) {
                throw new Exception($equipmentResult);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public function editArchive($data, $ids)
    {
        $rawData = $this->get($ids);
        if($data['amount'] < $rawData['amount']) {
            return __("The number of devices must not be less than the current value");
        }

        Db::startTrans();
        try {
            $data['id'] = $ids;
            $result = $this->isUpdate(true)->save($data);
            if (!$result) {
                throw new Exception($this->getError());
            }

            if($data['amount'] > $rawData['amount']) {
                $diff = $data['amount'] - $rawData['amount'];
                $equipmentModel = new Equipment();
                $equipmentResult = $equipmentModel->addEquipment($ids, $diff);
                if($equipmentResult != true) {
                    throw new Exception($equipmentResult);
                }
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }
}

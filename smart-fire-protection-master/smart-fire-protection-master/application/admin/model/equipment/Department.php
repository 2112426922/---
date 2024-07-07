<?php

namespace app\admin\model\equipment;

use think\Model;
use traits\model\SoftDelete;

class Department extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_department';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'equipment_manage_text',
        'status_text'
    ];

    public function getEquipmentManageList()
    {
        return [0 => __('Forbidden'), 1 => __('Allow')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getEquipmentManageTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['equipment_manage']) ? $data['equipment_manage'] : '');
        $list = $this->getEquipmentManageList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getSelectpicker()
    {
        $where = ['status' => 'normal'];
        return $this::where($where)->order('id desc')->column('id, name');
    }
}

<?php

namespace app\admin\model\equipment;

use think\Model;
use traits\model\SoftDelete;

class Supplier extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_supplier';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'relationship_text',
        'status_text'
    ];

    public function getRelationshipList()
    {
        return [
            'special' => __('Special'),
            'important' => __('Important'),
            'general' => __('General'),
            'standby' => __('Standby')
        ];
    }

    public function getRelationshipTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['relationship']) ? $data['relationship'] : '');
        $list = $this->getRelationshipList();
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

    public function getSelectpicker()
    {
        $where = ['status' => 'normal'];
        return $this::where($where)->order('id desc')->column('id, concat("【", supplier_code, "】", name)');
    }
}

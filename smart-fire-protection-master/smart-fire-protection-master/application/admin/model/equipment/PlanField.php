<?php

namespace app\admin\model\equipment;

use think\Model;
use traits\model\SoftDelete;

class PlanField extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_plan_field';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'require_text',
        'status_text',
        'type_text'
    ];

    public function getRequireList()
    {
        return [0 => __('NotRequire'), 1 => __('IsRequire')];
    }

    public function getRequireTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['require']) ? $data['require'] : '');
        $list = $this->getRequireList();
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

    public function getTypeList()
    {
        return ['text' => __('Text'), 'textarea' => __('Textarea'), 'true-false' => __('TrueFalse'), 'radio' => __('Radio'), 'multiple' => __('Multiple'), 'number' => __('Number'), 'image' => __('Image')];
    }

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function addFields($planId, $fields)
    {
        $list = [];
        foreach ($fields as $index => $field) {
            $item = $field;
            $item['plan_id'] = $planId;
            $item['name'] = 'input_' . $index;
            $item['options'] = json_encode($item['options']);
            unset($item['id'], $item['type_text']);

            $list[] = $item;
        }

        $result = $this->saveAll($list);
        if (!$result) {
            return $this->getError();
        }

        return true;
    }
}

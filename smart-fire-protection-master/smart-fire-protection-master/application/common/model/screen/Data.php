<?php

namespace app\common\model\screen;

use think\Config;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\helper\Str;
use think\Hook;
use think\Model;
use think\Request;
use think\Response;
use traits\model\SoftDelete;

class Data extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'screen_data';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'type_text'
    ];
    

    
    public function getTypeList()
    {
        return ['2' => __('Type 2'), '3' => __('Type 3')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['type'] ?? '');
        $list = $this->getTypeList();
        return $list[$value] ?? '';
    }



    public function setApiData()
    {
        $this->visible = ['id', 'setCode', 'setName', 'dataSetParamDtoList', 'setParamList'];
        $this['setCode'] = $this['code'];
        $this['setName'] = $this['title'];
        $this['id'] = $this['code'];

        $params = json_decode($this['params'], true) ?? [];
        $dataSetParamDtoList = [];
        foreach ($params as $param) {
            $dataSetParamDtoList[] = [
                'paramName' => $param['key'],
                'paramDesc' => $param['name'],
                'setCode' => $this['code'],
                'sampleItem' => '', // 默认值
            ];
        }

        $this['dataSetParamDtoList'] = $dataSetParamDtoList;
        $this['setParamList'] = json_decode($this['key_list']);
    }


    // 获取数据
    public function gainData($params = [])
    {
        $defaultParams = json_decode($this['params'], true);
        foreach ($defaultParams as $item) {
            if (!isset($params[$item['key']]) || $params[$item['key']] === '') {
                $params[$item['key']] = $item['value'];
            }
        }

        try{
            if ($this['type'] == 2) {
                $sql = $this['sql_content'];
                $prefix = Config::get('database.prefix');
                $sql = str_replace('__PREFIX__', $prefix, $sql);
                $sql = self::formatParams($sql, $params);
                $data = db()->query($sql);
            }
            if ($this['type'] == 3) {
                $hookName = Str::studly('screen_'.$this['hook_name']);
                $hookTag = Str::camel('screen_'.$this['hook_name']);
                $is = Hook::get($hookTag);
                // 加载hook
                if (empty($is)) {
                    Hook::add($hookTag, 'addons\\screen\\behavior\\'.$hookName);
                }
                $data = Hook::listen($hookTag, $params, null, true);
            }
        }catch (\Exception $e) {
            $result = [
                'code' => 0,
                'msg'  => "{$this['title']}获取失败-{$e->getMessage()}",
                'time' => Request::instance()->server('REQUEST_TIME'),
                'data' => [],
            ];
            $response = Response::create($result, 'json', 0);
            throw new HttpResponseException($response);
        }
        return $data;
    }

    // 格式化模板数据
    public static function formatParams($content, $params) {
        if (preg_match_all("/(?<=({{)).+?(?=}})/", $content, $matches)) {
            foreach ($matches[0] as $k => $field) {
                $fieldVal = $params[$field] ?? null;
                if ($fieldVal !== null) {
                    $content = str_replace("{{" . $field . "}}", $fieldVal, $content);
                }
            }
        }

        return $content;
    }

}

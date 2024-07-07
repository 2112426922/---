<?php

namespace addons\screen\controller;

use app\common\controller\Api;
use think\addons\Controller;

class Excel extends Base
{

    protected $needShare = ['read', 'data'];

    public function read()
    {
        $code = input('code', input('reportCode'));
        $row = \app\common\model\screen\Excel::get(['code' => $code]);
        if (!$row || $row['status'] == 'hidden') {
            $this->error('报表不存在');
        }

        if ($this->share) {
            if ($this->share['excel_id'] != $row['id']) {
                $this->error('分享链接不正确');
            }
        }

        $data = [];
        if ($row['content'] && $row['content'] != '[]') {
            $data['jsonStr'] = $row['content'];
            $data['setCodes'] = $row['data_code'];
            $data['setParam'] = $row['data_param'];
        }

        $this->success('', $data ?: null);
    }

    public function edit()
    {
        if (!$this->request->isPost()) {
            $this->error('method错误');
        }

        $row = \app\common\model\screen\Excel::get(['code' => input('reportCode')]);
        if (!$row || $row['status'] == 'hidden') {
            $this->error('报表不存在');
        }

        $row['content'] = html_entity_decode(input('jsonStr'));
        $row['data_code'] = input('setCodes');
        $row['data_param'] = html_entity_decode(input('setParam'));;
        $row->save();

        $this->success('保存成功');
    }


    public function data()
    {
        $code = input('code', input('reportCode'));
        $row = \app\common\model\screen\Excel::get(['code' => $code]);
        if (!$row || $row['status'] == 'hidden') {
            $this->error('报表不存在');
        }

        if ($this->share) {
            if ($this->share['excel_id'] != $row['id']) {
                $this->error('分享链接不正确');
            }
        }

        $setParam = input('setParam');
        if ($setParam) {
            $setParam = json_decode(html_entity_decode($setParam), true);
        }

        $data = [];
        $data['jsonStr'] = $row['content'];
        $data['setCodes'] = $row['data_code'];
        $data['setParam'] = $row['data_param'];


        $data['jsonStr'] = json_decode($data['jsonStr'], true) ?? [];
        foreach ($data['jsonStr'] as &$datum) {
            unset($datum['data']);

            $celldatumList = [];
            foreach ($datum['celldata'] as $celldatum) {
                $pattern = "/^#\{([^}]+)\}$/";
                $str = $celldatum['v']['m'] ?? '';

                if (preg_match($pattern, $str, $matches)) {
                    $result = $matches[1];
                    $result = explode('.', $result);
                    $dataRow = \app\common\model\screen\Data::get(['code' => $result[0]]);
                    $gainData = $dataRow->gainData($setParam && isset($setParam[$result[0]]) ? $setParam[$result[0]] : []);
                    $_list = array_column($gainData, $result[1]);
                    foreach ($_list as $key=>$item) {
                        $celldatum['v']['m'] = $item;
                        $celldatum['v']['v'] = $item;
                        $celldatumList[] = $celldatum;
                        $celldatum['r'] += 1;
                    }
                } else {
                    $celldatumList[] = $celldatum;
                }

            }

            $datum['celldata'] = $celldatumList;
        }

        $data['jsonStr'] = json_encode($data['jsonStr']);

        $this->success('', $data);
    }
}

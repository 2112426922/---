<?php

namespace addons\screen\controller;

use app\common\controller\Api;
use think\addons\Controller;

class Data extends Base
{

    protected $needShare = ['*'];

    public function index()
    {
        $list = \app\common\model\screen\Data::order('id','desc')->select();

        foreach ($list as $item) {
            $item->setApiData();
        }

        $this->success('', $list);
    }

    public function read()
    {
        $code = input('code');
        $row = \app\common\model\screen\Data::get(['code' => $code]);
        if (!$row) {
            $this->error('数据不存在');
        }
        
        $row->setApiData();
        
        $this->success('', $row);
    }

    public function data()
    {
        $code = input('setCode');
        if (!$code) {
            $this->success('', []);
        }

        $row = \app\common\model\screen\Data::get(['code' => $code]);
        if (!$row) {
            $this->error('数据不存在');
        }

        $data = $row->gainData(input('contextData/a'));
        $this->success('', $data);
    }
}

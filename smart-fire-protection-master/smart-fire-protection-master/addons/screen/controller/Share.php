<?php

namespace addons\screen\controller;

use app\common\controller\Api;
use think\addons\Controller;

class Share extends Base
{

    protected $needLogin = [];

    public function read()
    {
        $code = input('code');
        $row = \app\common\model\screen\Share::get(['code' => $code]);
        if (!$row) {
            $this->error('分享不存在');
        }
        if ($row['status'] == 'hidden') {
            $this->error('分享已关闭');
        }

        if ($row['end_time'] <= time()) {
            $this->error('分享已失效');
        }

        $page = \app\common\model\screen\Page::get($row['page_id']);
        if (!$page) {
            $page = $row->excel;
        }

        if (!$page || $page['status'] == 'hidden') {
            $this->error('大屏不存在');
        }

        $row->visible(['reportCode','shareCode','sharePassword','shareToken']);

        $row['reportCode'] = $page['code'];
        $row['shareCode'] = $row['code'];
        $row['sharePassword'] = '';
        $row['shareToken'] = $row['share_token'];

        $this->success('', $row);
    }
}

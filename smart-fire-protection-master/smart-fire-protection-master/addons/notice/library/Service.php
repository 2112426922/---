<?php

namespace addons\notice\library;


use app\admin\library\Auth;
use app\admin\model\equipment\Caution;
use EasyWeChat\Factory;
use app\admin\model\equipment\Repair;
class Service
{

    // 后台消息面板获取最新消息
    public static function getNoticeData($adminId = 0)
    {
//        return ['list' => [], 'num' => 0];
        if (!$adminId) {
            $adminId = (Auth::instance())->id;
        }

        $dataList = \app\admin\model\notice\Notice::where('to_id', $adminId)
            ->where('platform', 'admin')
            ->where('type','msg')
            ->order('id', 'desc')
            ->whereNull('readtime')
            ->limit(0,3)
            ->select();
        $list = [];
        foreach ($dataList as $item) {
            $list[] = [
                'title' => $item['content'],
                'time' => datetime($item['createtime'], "m-d H:i"),
                'url' => $item->ext_arr['url'] ?? '',
                'atitle' => $item->ext_arr['url_title'],
                'class' => $item->ext_arr['url_type'] == 2 ? 'btn-dialog' : ($item->ext_arr['url_type'] == 1 ? 'btn-addtabs' : ''),
            ];
        }

        $num = \app\admin\model\notice\Notice::where('to_id', $adminId)
            ->where('platform', 'admin')
            ->where('type','msg')
            ->order('id', 'desc')
            ->whereNull('readtime')
            ->count();

        $res = ['list' => $list, 'num' => $num];

        return $res;
    }

    // 后台消息面板获取待办任务  
    public static function getWaitData($adminId = 0)
    {
        //        return ['list' => [], 'num' => 0];
        $waitList = [
            [
                'title' => '有{$num}个工单待审核',
                'url' => 'equipment/repair/index',
                'atitle' => '维修订单',
               'num' => Repair::where('status','pending')->count(),
                'class' => 'btn-dialog',
                'time' => '',
            ],
            [
                'title' => '有{$num}个报警信息',
                'url' => 'equipment/caution/index',
                'atitle' => '设备报警',
                'num' => Caution::whereTime('createtime', 'd')->count(),
                'time' => '',
                'class' => 'btn-addtabs',
            ]
        ];
        $_row = Repair::where('status','pending')->order('id','desc')->find();
        if ($_row) {
            $waitList[0]['time'] = date('m-d H:i', $_row['createtime']);
        }

        $_row = Caution::whereTime('createtime', 'd')->order('id','desc')->find();
        if ($_row) {
            $waitList[1]['time'] = date('m-d H:i', $_row['createtime']);
        }


        $waitList = array_filter($waitList, function ($row) {
            return $row['num'] > 0;
        });
        $waitList = array_values($waitList);
        $waitNum = array_sum(array_column($waitList, 'num'));
        foreach ($waitList as &$item) {
            $item['title'] = str_replace('{$num}', "<span style='color: #F0C067; padding: 0 5px;'>{$item['num']}</span>", $item['title']);
        }

        $res = ['list' => $waitList, 'num' => $waitNum];
        return $res;
    }

    /**
     * 获取 easyWechat 公众号实例
     *
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public static function getOfficialAccount()
    {
        $appId = get_addon_config('notice')['app_id'] ?? '';
        $secret = get_addon_config('notice')['secret'] ?? '';

        if (empty($appId)) {
            $config = get_addon_config('third');
            if ($config) {
                $appId = $config['wechat']['app_id'] ?? '';
                $secret = $config['wechat']['app_secret'] ?? '';
            }
        }

        if (empty($appId)) {
            $config = get_addon_config('epay');
            if ($config) {
                $appId = $config['wechat']['app_id'] ?? '';
                $secret = $config['wechat']['app_secret'] ?? '';
            }
        }

        $config = [
            'app_id' => $appId,
            'secret' => $secret,
            'response_type' => 'array',
        ];
        $app = Factory::officialAccount($config);
        return $app;
    }

    /**
     * 获取 easyWechat 小程序实例
     *
     */
    public static function getMini()
    {
        $appId = get_addon_config('notice')['mini_app_id'] ?? '';
        $secret = get_addon_config('notice')['mini_secret'] ?? '';

        if (empty($appId)) {

        }

        $config = [
            'app_id' => $appId,
            'secret' => $secret,
            'response_type' => 'array',
        ];

        $app = Factory::miniProgram($config);
        return $app;
    }
}

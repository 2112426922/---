<?php
/**
 * qq: 1123416584
 * Time: 2022/4/1 11:46 上午
 */

namespace addons\notice\library;


use GatewayClient\Gateway;
use think\Hook;

class GatewayTool
{
    public static function init()
    {
        $register_port = get_addon_config('notice')['register_port'] ?? '';
        Gateway::$registerAddress = '127.0.0.1:'.$register_port;
    }

    public static function sendByUid($uid, $type, $data)
    {
        $data = [
            'type' => $type,
            'data' => $data,
        ];

        // 判断是否为后台，是后台的话，获取消息面板数据
        $uidArr = explode('_', $uid);
        if ($uidArr[0] == 'admin') {
            Hook::add('app_end',function() use ($uidArr, $uid){
                $waitData = Service::getWaitData($uidArr[1]);
                $noticeData = Service::getNoticeData($uidArr[1]);
                $noticePanel = [
                    'data' => ['wait_data' => $waitData, 'notice_data' => $noticeData],
                    'type' => 'notice_panel'
                ];
                Gateway::sendToUid($uid, json_encode($noticePanel, true));
            });
        }

        Gateway::sendToUid($uid, json_encode($data, true));
    }
}
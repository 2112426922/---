<?php

namespace addons\notice\library\server;

use addons\notice\library\GatewayTool;
use addons\notice\library\ToData;
use GatewayClient\Gateway;

class Msg
{

    // 获取通知数据
    public function getNoticeData($event, $template, $params)
    {
        $toData = ToData::get($event, $template, $params);

        $params = array_merge($params, $toData['default_field']);
        $content = self::formatParams($template['content'], $params);

        if ($template['platform'] == 'admin' || $template['platform'] == 'user') {
            foreach ($toData['to_id'] as $id) {
                try{
                    $num = \app\admin\model\notice\Notice::where('to_id', $id)
                        ->where('platform', $template['platform'])
                        ->where('type','msg')
                        ->order('id', 'desc')
                        ->whereNull('readtime')
                        ->count();

                    // 判断是否开启socket通知
                    $config = get_addon_config('notice');
                    if ($config['admin_real'] == 2 && $template['platform'] == 'admin') {
                        GatewayTool::sendByUid($template['platform'].'_'.$id, 'new_notice', ['msg' => $content, 'num' => $num+1, 'time' => time()]);
                    }
                    if ($config['user_real'] == 2 && $template['platform'] == 'user') {
                        GatewayTool::sendByUid($template['platform'].'_'.$id, 'new_notice', ['msg' => $content, 'num' => $num+1, 'time' => time()]);
                    }

                }catch (\Exception $e) {

                }
            }
        }

        // url
        $url = Msg::formatParams($template['url'], $params);
        $urlTitle = Msg::formatParams($template['url_title'], $params);
        $urlExt = [
            'url' => $url,
            'url_type' => $template['url_type'],
            'url_title' => $urlTitle
        ];
        $ext = json_encode($urlExt, true);

        return [
            'to_id' => $toData['to_id'],
            'content' => $content,
            'ext' => $ext
        ];
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
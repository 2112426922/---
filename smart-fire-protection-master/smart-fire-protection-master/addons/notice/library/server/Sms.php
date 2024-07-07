<?php

namespace addons\notice\library\server;

use addons\notice\library\ToData;
use hnh\custom\Log;

class Sms
{
    // 获取通知数据
    public function getNoticeData($event,$template, $params)
    {

        // 接收信息
        $toData = [
            'to_id' => [],
            'email' => [],
            // 默认字段
            'default_field' => [],
            // 接受消息手机号
            'mobile' => [],
        ];
        $toData = ToData::get($event, $template, $params);
        $toData['mobile'] = $toData['mobile'] ?? [];

        // 默认字段
        $params = array_merge($params, $toData['default_field']);
        $content = self::formatParams($template['content'], $params);
        $templateData = $template['mptemplate_json'];
        $templateData = json_decode($templateData, true) ?? [];
        foreach ($templateData as $k=>$v) {
            $v = self::formatParams($v, $params);
            $templateData[$k] = $v;
        }
        $to_id = $toData['to_id'];

        try{
            // 发送短信通知
            $is = \app\common\library\Sms::notice(implode(',', $toData['mobile']), $templateData, $template['mptemplate_id']);
            if (!$is) {
                // 记录日志
                $logData = [
                    'name' =>  $event['name'].'-短信发送失败',
                    'event' => $event->toArray(),
                    'params' => $params,
                    'error' => '发送失败'
                ];
                Log::error($logData);
            }
        }catch (\Exception $e) {
            // 记录日志
            $logData = [
                'name' =>  $event['name'].'-短信发送失败',
                'event' => $event->toArray(),
                'params' => $params,
            ];
            Log::catch('邮件发送失败', $e, $logData);
        }

        return [
            'to_id' => $to_id,
            'content' => $content,
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
<?php

namespace addons\notice\library\server;

use addons\notice\library\ToData;
use hnh\custom\Log;
use app\common\model\User;
use think\Exception;

class Email
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
        ];
        $toData = ToData::get($event, $template, $params);

        // 默认字段
        $params = array_merge($params, $toData['default_field']);
        $content = self::formatParams($template['content'], $params);
        $to_id = $toData['to_id'];

        try{
            // 发送邮件通知
            $is = \app\common\library\Email::instance()
                ->to(implode(',', $toData['email']))
                ->subject($event['name']?:'邮件通知')
                ->message('<div style="min-height:550px; padding: 50px 20px 100px;">' . $content . '</div>')
                ->send();
            if (!$is) {
                // 记录日志
                $logData = [
                    'name' =>  $event['name'].'-邮件发送失败',
                    'event' => $event->toArray(),
                    'params' => $params,
                    'error' => \app\common\library\Email::instance()->getError()
                ];
                Log::error($logData);
            }
        }catch (\Exception $e) {
            // 记录日志
            $logData = [
                'name' =>  $event['name'].'-邮件发送失败',
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
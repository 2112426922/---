<?php

namespace addons\notice\library\server;

use addons\notice\library\Service;
use addons\notice\library\ToData;
use EasyWeChat\Factory;

/**
 * 小程序订阅消息
 * Class Miniapp
 *
 * @package addons\notice\library\server
 */
class Miniapp
{

    // 获取通知数据
    public function getNoticeData($event, $template, $params)
    {
        $toData = ToData::get($event, $template, $params);

        $params = array_merge($params, $toData['default_field']);
        $content = self::formatParams($template['content'], $params);

        $templateData = $template['mptemplate_json'];
        $templateData = json_decode($templateData, true) ?? [];
        foreach ($templateData as $k=>$v) {
            $v = self::formatParams($v, $params);
            $templateData[$k] = $v;
        }

       $app = Service::getMini();

        // url
        $url = Msg::formatParams($template['url'], $params);
        $urlTitle = Msg::formatParams($template['url_title'], $params);
        $urlExt = [
            'url' => $url,
            'url_type' => $template['url_type'],
            'url_title' => $urlTitle
        ];
        $ext = json_encode($urlExt, true);

        foreach ($toData['mptemplate_openid'] as $v) {
            $_data = [
                'template_id' => $template['mptemplate_id'], // 所需下发的订阅模板id
                'touser' => $v,     // 接收者（用户）的 openid
                'page' => $url,       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
                'data' => $templateData
            ];
            $app->subscribe_message->send($_data);
        }

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
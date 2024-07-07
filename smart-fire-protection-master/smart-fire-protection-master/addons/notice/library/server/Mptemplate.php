<?php

namespace addons\notice\library\server;

use addons\notice\library\Service;
use addons\notice\library\ToData;
use EasyWeChat\Factory;

class Mptemplate
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

       $app = Service::getOfficialAccount();

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
            $app->template_message->send([
                'touser' => $v,
                'template_id' => $template['mptemplate_id'],
                'data' => $templateData,
                'url' => $url
            ]);
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
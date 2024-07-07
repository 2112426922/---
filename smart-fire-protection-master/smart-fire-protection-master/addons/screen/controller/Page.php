<?php

namespace addons\screen\controller;

use app\common\controller\Api;
use think\addons\Controller;

class Page extends Base
{

    protected $needShare = ['read'];

    public function read()
    {
        $code = input('code');
        $row = \app\common\model\screen\Page::get(['code' => $code]);
        if (!$row || $row['status'] == 'hidden') {
            $this->error('大屏不存在');
        }

        if ($this->share) {
            if ($this->share['page_id'] != $row['id']) {
                $this->error('分享链接不正确');
            }
        }

        $data = json_decode($row['content'], true) ?? [];
        $data['reportCode'] = $code;
        if (empty($data['dashboard'])) {
            $data['dashboard'] = [
                "title"=>"大屏",
                "width"=>1920,
                "height"=>1080,
                "backgroundColor"=>"rgba(30, 30, 30, 1)",
                "backgroundImage"=>""
            ];
        }
        if (empty($data['widgets'])) {
            $data['widgets'] = [];
        }
        $data['dashboard']['widgets'] = $data['widgets'];
        $data['widgets'] = [];

        $dataCodes = [];
        foreach ($data['dashboard']['widgets'] as $datum) {
            if (!empty($datum['value']['data']['dynamicData']['setCode'])) {
                $dataCodes[] = $datum['value']['data']['dynamicData']['setCode'];
            }
        }

        $widgetsParams = \app\common\model\screen\Data::where('code','in', $dataCodes)->column('params', 'code');
        foreach ($widgetsParams as &$param) {
            $param = json_decode($param, true) ?? [];
            $param = array_combine(array_column($param, 'key'), array_column($param, 'value'));
        }


        if (isset($data['dashboard']) && isset($data['dashboard']['backgroundImage'])) {
            $data['dashboard']['backgroundImage'] = $this->buildImage('backgroundImage', $data['dashboard']['backgroundImage']);
        }

        if (isset($data['dashboard']['widgets'])) {
            foreach ($data['dashboard']['widgets'] as &$widget) {
                if (isset($widget['value']) && isset($widget['value']['setup'])) {
                    foreach ($widget['value']['setup'] as $key=>$val) {
                        $widget['value']['setup'][$key] = $this->buildImage($key, $val);
                    }
                }
            }
            unset($widget);
        }

        foreach ($data['dashboard']['widgets'] as &$datum) {
            $datum['value']['position']['left'] = $datum['value']['position']['left']*1;
            $datum['value']['position']['top'] = $datum['value']['position']['top']*1;
            $datum['value']['position']['width'] = $datum['value']['position']['width']*1;
            $datum['value']['position']['height'] = $datum['value']['position']['height']*1;

            if (isset($datum['value']['data']['dynamicData']['contextData'])) {
                $contextData = $datum['value']['data']['dynamicData']['contextData'] ?? [];
                $contextData = array_merge($widgetsParams[$datum['value']['data']['dynamicData']['setCode']]??[], $contextData);
                $datum['value']['data']['dynamicData']['contextData'] = $contextData;

                foreach ($datum['options']['data'] as &$item2) {
                    if ($item2['type'] == 'dycustComponents') {
                        $item2['value']['contextData'] = $contextData;
                    }
                }
            }

        }

        $this->success('', $data);
    }

    // 判断是否为图片并补全图片路径
    protected function buildImage($key, $val)
    {
        if (false == ((stripos($key, 'image') !== false || stripos($key, 'img') !== false))) {
            return $val;
        }
        if (!$val) {
            return $val;
        }
        if (!is_string($val)) {
            return $val;
        }

        $imageUrl = cdnurl($val, true);

        return $imageUrl;
    }

    public function edit()
    {
        if (!$this->request->isPost()) {
            $this->error('method错误');
        }

        $content = html_entity_decode(input('content', null));
        $contentArr = json_decode($content, true);
        $code = $contentArr['reportCode'] ?? '';

        $row = \app\common\model\screen\Page::get(['code' => $code]);
        if (!$row || $row['status'] == 'hidden') {
            $this->error('大屏不存在');
        }

        $row->save(['content' => $content]);

        $this->success('保存成功');
    }
}

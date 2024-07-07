<?php
/**
 * User: 乃火
 * Time: 2023/9/14 10:46 上午
 * QQ: 1123416584
 */

namespace addons\screen\behavior;
use app\admin\model\AdminLog;
use app\common\model\screen\Data;
use app\common\model\User;


class ScreenTestGetFamap
{
    // 演示高德地图使用
    public function screenTestGetFamap($params)
    {
        $list = [
            [
                'lng' => '106.551556',
                'lat' => '29.563009',
                'city' => '重庆市',
            ],
            [
                'lng' => '121.473701',
                'lat' => '31.230416',
                'city' => '上海市',
            ],
            [
                'lng' => '116.407526',
                'lat' => '39.90403',
                'city' => '北京市',
            ],
            [
                'lng' => '104.066541',
                'lat' => '30.572269',
                'city' => '成都市',
            ],
            [
                'lng' => '117.200983',
                'lat' => '39.084158',
                'city' => '天津市',
            ],
            [
                'lng' => '113.264434',
                'lat' => '23.129162',
                'city' => '广州市',
            ],
            [
                'lng' => '115.464806',
                'lat' => '38.873891',
                'city' => '保定市',
            ],
            [
                'lng' => '126.534967',
                'lat' => '45.803775',
                'city' => '哈尔滨市',
            ],
        ];

        if (!empty($params['city'])) {
            $list = array_filter($list, function ($row) use ($params) {
                return mb_stripos($row['city'], $params['city']) !== false;
            });
        }

        $images = [
            'http://fastaddon.hnh117.com/uploads/20231009/032472fe6c1b910df107f9c81aa57e47.png',
            'http://fastaddon.hnh117.com/uploads/20231009/c576028854ed681535c3e41fecd3a0e3.png'
        ];

        foreach ($list as &$item) {
            $item['image'] = $images[array_rand($images)];
            $item['num'] = rand(10000, 99999);
        }

        return $list;
    }
}
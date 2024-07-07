<?php
/**
 * User: 乃火
 * Time: 2023/9/14 10:46 上午
 * QQ: 1123416584
 */

namespace addons\screen\behavior;
use app\common\model\screen\Data;
use app\common\model\User;


class ScreenDemo
{
    // 演示demo
    public function screenDemo($params)
    {
        return [['num' => User::count()]];
    }
}
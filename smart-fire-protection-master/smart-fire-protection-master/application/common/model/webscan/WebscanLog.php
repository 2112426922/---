<?php

namespace app\common\model\webscan;

use think\Model;

/**
 * 攻击日志
 */
class WebscanLog extends Model
{
    protected $autoWriteTimestamp = 'int';
    protected $updateTime = false;
}

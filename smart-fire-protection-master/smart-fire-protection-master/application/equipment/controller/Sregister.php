<?php

namespace app\equipment\controller;

use GatewayWorker\Register;
use Workerman\Worker;

// 自动加载类
require_once __DIR__ . '/../../../addons/equipment/library/GatewayWorker/vendor/autoload.php';

/**
 * Win下启动 register服务 专用类
 */
class Sregister
{
    public function __construct()
    {
        // register 服务必须是text协议
        $register = new Register('text://0.0.0.0:1238');
        // 如果不是在根目录启动，则运行runAll方法
        if (!defined('GLOBAL_START')) {
            Worker::runAll();
        }
    }
}
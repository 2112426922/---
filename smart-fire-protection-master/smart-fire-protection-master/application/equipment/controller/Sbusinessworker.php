<?php

namespace app\equipment\controller;

use GatewayWorker\BusinessWorker;
use Workerman\Worker;

// 自动加载类
require_once __DIR__ . '/../../../addons/equipment/library/GatewayWorker/vendor/autoload.php';

/**
 * Win下启动 businessworker服务 专用类
 */
class Sbusinessworker
{

    function __construct()
    {
        // bussinessWorker 进程
        $worker = new BusinessWorker();
        // worker名称
        $worker->name = 'EquipmentBusinessWorker';
        // bussinessWorker进程数量
        $worker->count = 4;
        // 服务注册地址
        $worker->registerAddress = '127.0.0.1:1238';
        //设置处理业务的类,此处制定Events的命名空间
        $worker->eventHandler = 'addons\equipment\library\GatewayWorker\Applications\Equipment\Events';

        // 如果不是在根目录启动，则运行runAll方法
        if (!defined('GLOBAL_START')) {
            Worker::runAll();
        }
    }
}
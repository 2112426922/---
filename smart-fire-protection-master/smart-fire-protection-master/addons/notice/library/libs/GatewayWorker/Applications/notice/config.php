<?php

$noticeConfig = require __DIR__. '/../../../../../config.php';


$config = [
    // WebSocket端口,请在安全组、防火墙等开放此端口
    'websocket_port' => 6006,
    // 服务注册端口 无需对外开放,属未被占用的端口即可
    'register_port' => 6206,
    // 内部通讯起始端口,无需对外开放,属未被占用的端口即可
    'internal_start_port' => 6406,

    'wss_switch' => false,
    'ssl_cert' => '',
    'ssl_cert_key' => ''
];

foreach ($noticeConfig as $item) {
    if (isset($config[$item['name']])) {
        $config[$item['name']] = $item['value'];
    }
}

return $config;
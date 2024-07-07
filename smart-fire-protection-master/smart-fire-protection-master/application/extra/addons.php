<?php

return [
    'autoload' => false,
    'hooks' => [
        'admin_nologin' => [
            'adminlogin',
        ],
        'module_init' => [
            'adminlogin',
            'apilog',
        ],
        'view_filter' => [
            'adminlogin',
            'clicaptcha',
            'darktheme',
        ],
        'response_send' => [
            'apilog',
            'lockscreen',
        ],
        'config_init' => [
            'buiattach',
            'cropper',
            'darktheme',
            'lockscreen',
            'nkeditor',
            'notice',
        ],
        'upload_after' => [
            'buiattach',
        ],
        'action_begin' => [
            'clicaptcha',
        ],
        'captcha_mode' => [
            'clicaptcha',
        ],
        'app_init' => [
            'equipment',
            'log',
            'notice',
            'qrcode',
            'webscan',
        ],
        'upgrade' => [
            'famysql',
        ],
        'admin_login_init' => [
            'lockscreen',
        ],
        'lockscreenhook' => [
            'lockscreen',
        ],
        'user_sidenav_after' => [
            'notice',
        ],
        'send_notice' => [
            'notice',
        ],
        'prismhook' => [
            'prism',
        ],
    ],
    'route' => [
        '/qrcode$' => 'qrcode/index/index',
        '/qrcode/build$' => 'qrcode/index/build',
    ],
    'priority' => [],
    'domain' => '',
];

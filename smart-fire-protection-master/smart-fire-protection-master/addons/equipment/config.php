<?php

return [
    [
        'name' => '__tips__',
        'title' => '温馨提示',
        'type' => 'string',
        'content' => [],
        'value' => '1. 二维码短域名：系统生成的设备二维码将含该域名信息，通过微信小程序后台“扫普通链接二维码打开小程序”板块绑定该域名后，微信扫“设备二维码”便能直接打开小程序，而无需先打开小程序再扫码。<br>2. 开启短信提醒前务必先安装「腾讯云短信插件」，并开通相关服务。<br>具体使用说明请看插件文档。',
        'rule' => '',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'weappid',
        'title' => '微信小程序AppID',
        'type' => 'string',
        'content' => [],
        'value' => '',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'weappsecret',
        'title' => '微信小程序Secret',
        'type' => 'string',
        'content' => [],
        'value' => '',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'shorturl',
        'title' => '二维码短域名',
        'type' => 'string',
        'content' => [],
        'value' => '',
        'rule' => '',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'manage_phone',
        'title' => '管理员电话',
        'type' => 'string',
        'content' => [],
        'value' => '',
        'rule' => '',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'repair_assign_oneself',
        'title' => '管理人员报修是否默认派给自己',
        'type' => 'radio',
        'content' => [
            '否',
            '是',
        ],
        'value' => '0',
        'rule' => 'required',
        'msg' => '',
        'tip' => '设备管理权限部门人员提交设备报修后维修工单默认分配给自己',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'remind_assign_repair',
        'title' => '是否开启维修工单派单短信提醒',
        'type' => 'radio',
        'content' => [
            '否',
            '是',
        ],
        'value' => '0',
        'rule' => 'required',
        'msg' => '',
        'tip' => '提醒后台派单人员处理新工单分配任务',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'remind_receive_repair',
        'title' => '是否开启维修工单接单短信提醒',
        'type' => 'radio',
        'content' => [
            '否',
            '是',
        ],
        'value' => '0',
        'rule' => 'required',
        'msg' => '',
        'tip' => '提醒设备管理权限部门人员自行接单，短信下发量可能会很大',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'remind_deal_repair',
        'title' => '是否开启维修工单下发短信提醒',
        'type' => 'radio',
        'content' => [
            '否',
            '是',
        ],
        'value' => '0',
        'rule' => 'required',
        'msg' => '',
        'tip' => '后台派单后提醒相关维修人员及时处理',
        'ok' => '',
        'extend' => '',
    ],
];
<?php

namespace addons\equipment\library;

use think\Db;
use GatewayWorker\Lib\Gateway;

/**
 *
 */
class Common
{

    public function __construct()
    {

    }
        /**
     * 发送消息
     * @param string client_id 链接ID
     * @param string msg 消息内容
     */
    public static function showMsg($client_id, $msg = '')
    {
        Gateway::sendToClient($client_id, json_encode([
            'code'    => 0,
            'msgtype' => 'show_msg',
            'msg'     => $msg,
        ]));
    }
    /*
     * 检查/过滤变量
     */
    public static function checkVariable(&$variable)
    {
        $variable = htmlspecialchars($variable);
        $variable = stripslashes($variable); // 删除反斜杠
        $variable = addslashes($variable); // 转义特殊符号
        $variable = trim($variable); // 去除字符两边的空格
    }
}

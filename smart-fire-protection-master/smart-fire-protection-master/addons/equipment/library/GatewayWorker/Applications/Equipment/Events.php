<?php
/**
 * workerman业务函数，处理下位机发送的数据
 */
namespace addons\equipment\library\GatewayWorker\Applications\Equipment;
use \GatewayWorker\Lib\Gateway;
use think\Db;
use addons\equipment\library\Common;

class Events
{
     public static function onConnect($client_id)
     {
        $data['type'] = 'get_client_id';
        $data['data'] = ['client_id' => $client_id];
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, json_encode($data, JSON_UNESCAPED_UNICODE));
     }
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
    public static function onMessage($client_id, $message)
    {
        // 分发到控制器
        $message_data = json_decode($message,true);
        if(!$message_data){
            return;
        }
        // 安全检查
        array_walk_recursive($message_data, ['addons\equipment\library\Common', 'checkVariable']);
        if (!is_array($message_data) || !isset($message_data['code']) || !isset($message_data['type'])) {

            common::showMsg($client_id,'错误的请求！');
            return;
        }
        $nowTime = time();
        $archive_id=Db::name('equipment_equipment')->where('equipment_code', $message_data['code'])->value('archive_id');
        switch($message_data['type']){
            case 0:
                $code = $message_data['code'];
                $temp = $message_data['temp'];
                $hum = $message_data['hum'];
                $press = $message_data['press'];
                $light = $message_data['light'];
                $sta = $message_data['sta'];

                Db::name('equipment_message')->where('code', $code)
                ->update([
                    'temp'       => $temp,
                    'hum'    => $hum,
                    'press' => $press,
                    'light' => $light,
                    'sta' => $sta,
                    'updatetime' => $nowTime
                ]);
                Db::name('equipment_messagelog')
                ->insert([
                    'code'        => $code,
                    'temp'       => $temp,
                    'hum'    => $hum,
                    'press' => $press,
                    'light' => $light,
                    'sta' => $sta,
                    'archive_id' => $archive_id,
                    'createtime' => $nowTime
                ]);
            return;
            case 1:
                $code = $message_data['code'];
                $warning = $message_data['warning'];
                Db::name('equipment_caution')
                ->insert([
                    'code'        => $code,
                    'warning'       => $warning,
                    'archive_id' => $archive_id,
                    'createtime' => $nowTime,
                ]);
            return;
            case 2:
                $code = $message_data['code'];
                $state = $message_data['state'];
                Db::name('equipment_message')->where('code', $code)
                ->update([
                    'status'       => $state,
                    'updatetime' => $nowTime
                ]);
                Db::name('equipment_messagelog')
                ->insert([
                    'code'        => $code,
                    'status'       => $state,
                    'createtime' => $nowTime,
                    'archive_id' => $archive_id,
                ]);
            return;
            case 3:
                $code = $message_data['code'];
                $floor = $message_data['floor'];
                $x = $message_data['x'];
                $y = $message_data['y'];
                Db::name('equipment_message')->where('code', $code)
                ->update([
                    'floor'       => $floor,
                    'x'       => $x,
                    'y'       => $y,
                    'updatetime' => $nowTime
                ]);
                Db::name('equipment_messagelog')
                ->insert([
                    'code'        => $code,
                    'floor'       => $floor,
                    'x'       => $x,
                    'y'       => $y,
                    'createtime' => $nowTime,
                    'archive_id' => $archive_id,
                ]);
            return;
        }

    }
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
       // 向所有人发送 
       GateWay::sendToAll("$client_id logout\r\n");
   }
}

<?php
/**
 * User: 乃火
 * Time: 2021/2/21 1:16 下午
 * QQ: 1123416584
 */

namespace addons\notice\library;


use addons\third\Third;
use app\admin\model\Admin;
use app\admin\model\AuthGroup;
use app\admin\model\notice\AdminMptemplate;
use app\common\model\User;
use think\db\Query;
use think\Hook;

/**
 * 消息接收者处理
 * Class ToData
 *
 * @package addons\notice\library
 */
class ToData
{

    /**
     * 获取发送对象（可以自定义调整）
     */
    public static function get($event,$template, $params)
    {
        // 接收信息
        $toData = [
            'to' => [],
            // 所有接收者id,根据这个创建消息记录
            'to_id' => [],
            // 所有接收者email
            'email' => [],
            'mobile' => [],
            // 触发消息者user
            'user' => null,
            // 默认参数字段
            'default_field' => [],
            // 接收模版消息(公众号)openid
            'mptemplate_openid' => [],

            'template' => $template,
            'params' => $params,
            'event' => $event
        ];
        $to = [];
        $mptemplateOpenid = [];

        // 找出user_id用户
        if (isset($params['user_id'])) {
            $user =  User::get($params['user_id']);
        } else {
            $user = null;
        }

        // 前台通知
        if ($template['platform'] == 'user') {
            if ($user) {
                $to = [$user];
            }
        }
        // 后台通知
        if ($template['platform'] == 'admin') {
            $groupId = get_addon_config('notice')['only_admin_group_id'];
            if (!is_array($groupId)) {
                $groupId = explode(',', $groupId);
                $groupId = array_filter($groupId);
            }
            $receiver_admin_ids = $params['receiver_admin_ids'] ?? [];
            if (!is_array($receiver_admin_ids)) {
                $receiver_admin_ids = explode(',', $receiver_admin_ids);
                $receiver_admin_ids = array_filter($receiver_admin_ids);
            }
            $receiver_admin_group_ids = $params['receiver_admin_group_ids'] ?? [];
            if (!is_array($receiver_admin_group_ids)) {
                $receiver_admin_group_ids = explode(',', $receiver_admin_group_ids);
                $receiver_admin_group_ids = array_filter($receiver_admin_group_ids);
            }
            $groupId = array_merge($groupId, $receiver_admin_group_ids);

            if ($receiver_admin_ids) {
                $adminWhere = ['__TABLE__.id' => ['in', $receiver_admin_ids]];
            } else {
                $adminWhere = [];
            }
            if ($groupId) {
                $to = Admin::join('auth_group_access', 'auth_group_access.uid = id')
                    ->where('group_id', 'in', $groupId)
                    ->where($adminWhere)
                    ->select();
            } else {
                $to = Admin::where($adminWhere)->select();
            }
        }
        // 判断是否为邮件发送
        if ($template['type'] == 'email') {
            $to = array_filter($to, function($row) {
                return $row['email'];
            });
        }

        // 判断是否为短信发送
        if ($template['type'] == 'sms') {
            $to = array_filter($to, function($row) {
                return $row['mobile'];
            });
        }

        // 判断是否为后台模版通知
        if ($template['type'] == 'mptemplate' && $template['platform'] == 'admin') {
            $adminMptemplateList = AdminMptemplate::where('admin_id', 'in', array_column($to, 'id'))->select();
            $adminMptemplateAdminIds = array_column($adminMptemplateList, 'admin_id');
            foreach ($to as $k=>$v) {
                if (!in_array($v['id'], $adminMptemplateAdminIds)) {
                    unset($to[$k]);
                }
            }
            $to = array_values($to);
            $mptemplateOpenid = array_column($adminMptemplateList, 'openid');
        }


        // 判断是否为前台模版通知
        if ($template['type'] == 'mptemplate' && $template['platform'] == 'user') {
            // 有第三方登录表
            if (class_exists('\app\admin\model\Third')) {
                $userMptemplateList = \app\admin\model\Third::where('user_id', 'in', array_column($to, 'id'))
                    ->where('platform', 'wechat')
                    ->where(function(Query  $query) {
                        $query->where('apptype', 'mp')
                            ->whereOr('apptype', '');
                    })
                    ->select();
            } else {
                $userMptemplateList = [];
            }

            $userMptemplateAdminIds = array_column($userMptemplateList, 'user_id');
            foreach ($to as $k=>$v) {
                if (!in_array($v['id'], $userMptemplateAdminIds)) {
                    unset($to[$k]);
                }
            }
            $to = array_values($to);
            $mptemplateOpenid = array_column($userMptemplateList, 'openid');
        }

        // 判断是否为前台订阅消息
        if ($template['type'] == 'miniapp' && $template['platform'] == 'user') {
            // 有第三方登录表
            if (class_exists('\app\admin\model\Third')) {
                $userMptemplateList = \app\admin\model\Third::where('user_id', 'in', array_column($to, 'id'))
                    ->where('platform', 'wechat')
                    ->where(function(Query  $query) {
                        $query->where('apptype', 'miniapp')
                            ->whereOr('apptype', 'mini');
                    })
                    ->select();
            } else {
                $userMptemplateList = [];
            }

            $userMptemplateAdminIds = array_column($userMptemplateList, 'user_id');
            foreach ($to as $k=>$v) {
                if (!in_array($v['id'], $userMptemplateAdminIds)) {
                    unset($to[$k]);
                }
            }
            $to = array_values($to);
            $mptemplateOpenid = array_column($userMptemplateList, 'openid');
        }


        $toData['to'] = $to;
        $toData['mptemplate_openid'] = $mptemplateOpenid;
        $toData['email'] = array_column($to, 'email');
        $toData['mobile'] = array_column($to, 'mobile');
        $toData['to_id'] = array_column($to, 'id');
        $toData['user'] = $user;
        $toData['default_field'] = [
            'user_id' => $user['id'] ?? '',
            'user_nickname' => $user['nickname'] ?? '',
            'user_email' => $user['email'] ?? '',
            'user_mobile' => $user['mobile'] ?? '',
            'createtime_text' => date('Y-m-d H:i:s'),
            'createdate' => date('Y-m-d')
        ];


        Hook::listen('notice_to_data', $toData);

        if (count($toData['to_id']) == 0) {
            return_error('接收者不存在');
        }

        return $toData;
    }

}
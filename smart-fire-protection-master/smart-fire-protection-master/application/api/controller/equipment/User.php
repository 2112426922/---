<?php

namespace app\api\controller\equipment;

use app\admin\model\equipment\Department;
use app\admin\model\equipment\Staff;
use EasyWeChat\Factory as EasyWeChat;

class User extends Base
{
    protected $noNeedLogin = ['login', 'weapplogin'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        $this->token = $this->request->post('token');
        parent::_initialize();
    }

    /**
     * 登录
     * @return array
     */
    public function login()
    {
        $code = $this->request->post('code', '');
        $mobile = $this->request->post('mobile', '');
        $password = $this->request->post('password', '');
        if (!$mobile || !$password) {
            $this->error("参数不正确");
        }

        $result = $this->auth->login($mobile, $password);
        if (!$result) {
            $this->error($this->auth->getError());
        }

        $staff = Staff::where(['user_id' => $this->auth->id, 'status' => 'normal'])->field('id, department_id')->find();
        if (!$staff) {
            $this->auth->logout();
            $this->error("该账号不存在或已被禁用");
        }

        // 设备管理权限
        $equipmentManage = Department::where('id', $staff['department_id'])->value('equipment_manage');
        $data = array_merge(['equipment_manage' => $equipmentManage], $this->auth->getUserinfo());

        $config = get_addon_config('equipment');
        if (!empty($code) && $config['weappid'] && $config['weappsecret']) {
            $weappConfig = [
                'app_id' => $config['weappid'],
                'secret' => $config['weappsecret']
            ];
            $weapp = EasyWeChat::miniProgram($weappConfig);
            $code2Session = $weapp->auth->session($code);
            if (isset($code2Session['openid'])) {
                $openid = $code2Session['openid'];

                $staffModel = new Staff();
                $staffModel->isUpdate(true)->save(['openid' => ''], ['openid' => $openid]);
                $staffModel->isUpdate(true)->save(['openid' => $openid], ['id' => $staff['id']]);

                $data['openid'] = $openid;
            } else {
                $this->error($code2Session['errmsg'], '', -1);
            }
        }

        $this->success(__('Logged in successful'), $data);
    }

    /**
     * 微信小程序登录
     * @return array
     */
    public function weapplogin()
    {
        $code = $this->request->post('code', '');
        $openid = $this->request->post('openid', '');
        if (!$code && !$openid) {
            $this->error("参数不正确");
        }

        $config = get_addon_config('equipment');
        if (!$config || !$config['weappid'] || !$config['weappsecret']) {
            $this->error("请在后台插件管理填写小程序配置项");
        }

        if (!empty($openid)) {
            $this->tokenByOpenid($openid);
        }
        if (!empty($code)) {
            $weappConfig = [
                'app_id' => $config['weappid'],
                'secret' => $config['weappsecret']
            ];
            $weapp = EasyWeChat::miniProgram($weappConfig);
            $code2Session = $weapp->auth->session($code);
            if (isset($code2Session['openid'])) {
                return $this->tokenByOpenid($code2Session['openid']);
            } else {
                $this->error($code2Session['errmsg'], '', -1);
            }
        }
    }

    protected function tokenByOpenid($openid)
    {
        $staff = Staff::getByOpenid($openid);
        if (!$staff) {
            $this->error('该微信号暂未绑定员工账号');
        }
        if ($staff['status'] != 'normal') {
            $this->error("该账号已被禁用");
        }

        // 设备管理权限
        $equipmentManage = Department::where('id', $staff['department_id'])->value('equipment_manage');
        $baseInfo = ['openid' => $openid, 'equipment_manage' => $equipmentManage];

        //如果有传Token
        if ($this->token) {
            $this->auth->init($this->token);
            //检测是否登录
            if ($this->auth->isLogin() && ($staff['user_id'] == $this->auth->id)) {
                $data = array_merge($baseInfo, $this->auth->getUserinfo());
                $this->success(__('Logged in successful'), $data);
            }
        }

        // 写入登录Cookies和Token
        $result = $this->auth->direct($staff['user_id']);
        if ($result) {
            $data = array_merge($baseInfo, $this->auth->getUserinfo());
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    public function logout()
    {
        $userId = $this->auth->id;
        $staffModel = new Staff();
        $result = $staffModel->save(['openid' => ''], ['user_id' => $userId]);
        if ($result) {
            $this->auth->logout();
            $this->success(__('Logout successful'));
        } else {
            $this->error(__('Logout fail'));
        }
    }

    public function getStaffInfo()
    {
        $user = $this->auth->getUserinfo();
        $staff = Staff::getByUserId($user['id']);
        $department = Department::where(['id' => $staff['department_id']])->value('name');
        $result = [
            'id' => $user['id'],
            'nickname' => $user['nickname'],
            'mobile' => $user['mobile'],
            'avatar' => $user['avatar'],
            'workno' => $staff['workno'],
            'position' => $staff['position'],
            'department' => $department
        ];

        $this->success('', $result);
    }
}
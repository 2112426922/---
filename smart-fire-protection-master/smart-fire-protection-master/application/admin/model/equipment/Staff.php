<?php

namespace app\admin\model\equipment;

use app\admin\model\User;
use think\Db;
use think\Exception;
use think\Model;
use traits\model\SoftDelete;

class Staff extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'equipment_staff';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];


    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function addStaff($data)
    {
        Db::startTrans();
        try {
            // 注册会员
            $auth = \app\common\library\Auth::instance();
            $extend = ['nickname' => $data['nickname']];
            $result = $auth->register($data['mobile'], $data['password'], '', $data['mobile'], $extend);
            if (!$result) {
                throw new Exception($auth->getError());
            }

            $userinfo = $auth->getUserinfo();
            $params = [
                'user_id' => $userinfo['id'],
                'department_id' => isset($data['department_id']) ? $data['department_id'] : 0,
                'workno' => isset($data['workno']) ? $data['workno'] : '',
                'position' => isset($data['position']) ? $data['position'] : '',
                'status' => isset($data['status']) ? $data['status'] : 'normal'
            ];
            $saveResult = $this->insertGetId($params);
            if (!$saveResult) {
                throw new Exception($this->getError());
            }
            Db::commit();
            return $userinfo['id'];
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public function editStaff($data, $ids)
    {
        $rawData = $this->with(['user'])->find($ids);
        Db::startTrans();
        try {
            $userData = [];
            if ($data['nickname'] != $rawData['user']['nickname']) {
                $userData['nickname'] = $data['nickname'];
            }
            if ($data['mobile'] != $rawData['user']['mobile']) {
                // 检测手机号是否存在
                if (User::getByUsername($data['mobile']) || User::getByMobile($data['mobile'])) {
                    throw new Exception(__("Mobile already exist"));
                }

                $userData['username'] = $data['mobile'];
                $userData['mobile'] = $data['mobile'];
            }
            if (!empty($data['password'])) {
                $userData['password'] = $data['password'];
            }
            if (!empty($userData)) {
                $userModel = new User();
                $result = $userModel->isUpdate(true)->save($userData, ['id' => $rawData['user_id']]);
                if (!$result) {
                    throw new Exception($userModel->getError());
                }
            }

            $params = [
                'id' => $ids,
                'department_id' => $data['department_id'],
                'workno' => $data['workno'],
                'position' => $data['position'],
                'status' => $data['status']
            ];
            $saveResult = $this->isUpdate(true)->save($params);
            if (!$saveResult) {
                throw new Exception($this->getError());
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function multiUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function multiDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function getSelectpicker($keyword = [], $manage = 0)
    {
        $where = ['staff.status' => 'normal'];
        $query = $this::where($where);
        if (!empty($keyword)) {
            if (is_array($keyword)) {
                if (count($keyword) == 1) {
                    $query = $query->where('user.nickname|user.mobile', 'like', '%' . $keyword[0] . '%');
                } else {
                    $query = $query->where(function ($query) use ($keyword) {
                        foreach ($keyword as $item) {
                            $query->whereOr(function ($query) use ($item) {
                                $query->where('user.nickname|user.mobile', 'like', '%' . $item . '%');
                            });
                        }
                    });
                }
            } else {
                $query = $query->where('user.nickname|user.mobile', 'like', '%' . $keyword . '%');
            }
        }
        if ($manage) {
            $query = $query->where('department.equipment_manage', 1);
        }

        return $query->with(['user', 'department'])->order('staff.id desc')->column('user_id, concat(user.nickname, "，", user.mobile)');
    }
}

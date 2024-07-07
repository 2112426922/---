<?php

namespace app\admin\controller\shortcutmenu;

use app\common\controller\Backend;
use think\Db;
use fast\Tree;

/**
 * 快捷菜单
 *
 * @icon fa fa-circle-o
 */
class Menu extends Backend
{

    /**
     * Shortcutmenu模型对象
     * @var \app\admin\model\Shortcutmenu
     */
    protected $model = null;

    protected $noNeedRight = ['multi'];    //快捷菜单开关设置不用鉴权

    public function _initialize()
    {
        parent::_initialize();

        $selectData = array(
            ['id' => "", 'text' => ""],
            ['id' => "bg-red", 'text' => "红色"],
            ['id' => "bg-yellow", 'text' => "黄色"],
            ['id' => "bg-aqua", 'text' => "湖绿色"],
            ['id' => "bg-blue", 'text' => "蓝色"],
            ['id' => "bg-light-blue", 'text' => "浅蓝色"],
            ['id' => "bg-green", 'text' => "绿色"],
            ['id' => "bg-teal", 'text' => "青色"],
            ['id' => "bg-olive", 'text' => "橄榄色"],
            ['id' => "bg-lime", 'text' => "荧光绿"],
            ['id' => "bg-orange", 'text' => "橙色"],
            ['id' => "bg-fuchsia", 'text' => "紫红色"],
            ['id' => "bg-purple", 'text' => "紫色"],
            ['id' => "bg-maroon", 'text' => "红褐色"],
            ['id' => "bg-red-active", 'text' => "深红色"],
            ['id' => "bg-yellow-active", 'text' => "深黄色"],
            ['id' => "bg-aqua-active", 'text' => "深海蓝"],
            ['id' => "bg-light-blue-active", 'text' => "深亮蓝色"],
            ['id' => "bg-green-active", 'text' => "深绿色"],
            ['id' => "bg-teal-active", 'text' => "深青色"],
            ['id' => "bg-olive-active", 'text' => "深橄榄色"],
            ['id' => "bg-lime-active", 'text' => "深荧光绿"],
            ['id' => "bg-orange-active", 'text' => "深橙色"],
            ['id' => "bg-fuchsia-active", 'text' => "深紫红色"],
            ['id' => "bg-purple-active", 'text' => "深紫色"],
            ['id' => "bg-maroon-active", 'text' => "深褐色"]
        );


        $this->assignconfig('selectData', $selectData);

        $this->model = new \app\admin\model\shortcutmenu\Menu;

    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 快捷菜单
     */
    public function index()
    {

        $this->dataLimit = false;

        //获取当前用户的权限菜单
        $userRule = Db::name('auth_group')->alias('group')
            ->join('auth_group_access access', 'access.group_id=group.id')
            ->where('access.uid', $this->auth->id)
            ->field('group.id,group.name,group.rules')
            ->select();

        $arr = [];
        foreach ($userRule as $row) {
            $arrtmp = explode(",", $row['rules']);
            $arr = array_merge($arr, $arrtmp);

        }

        //查找是否有具体超级管理员权限
        $key = array_search('*', $arr);

        //如果具体超级管理员权限，则不过滤快捷菜单项
        if ($key === 0) {

            $menulist = Db::name('auth_rule')->alias('rule')
                ->join('shortcutmenu menu', 'menu.auth_rule_id=rule.id')
                ->field('menu.admin_id,rule.title,rule.`name`,menu.is_shortcut_menu,menu.menu_color,rule.pid,rule.icon')
                ->where(['rule.status' => 'normal', 'rule.ismenu' => 1, 'menu.is_shortcut_menu' => 1, 'menu.admin_id' => $this->auth->id])
                ->select();
        } else {
            $str = implode(',', $arr);

            $menulist = Db::name('auth_rule')->alias('rule')
                ->join('shortcutmenu menu', 'menu.auth_rule_id=rule.id')
                ->field('menu.admin_id,rule.title,rule.`name`,menu.is_shortcut_menu,menu.menu_color,rule.pid,rule.icon')
                ->where(['rule.status' => 'normal', 'rule.ismenu' => 1, 'menu.is_shortcut_menu' => 1, 'menu.admin_id' => $this->auth->id])
                ->where('rule.id', 'in', $str)
                ->select();

        }

        $menulist = collection($menulist)->toArray();

        $this->view->assign('menulist', $menulist);

        return $this->view->fetch();

    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {


            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();


            //获取当前用户的权限菜单
            $userRule = Db::name('auth_group')->alias('group')
                ->join('auth_group_access access', 'access.group_id=group.id')
                ->where('access.uid', $this->auth->id)
                ->field('group.id,group.name,group.rules')
                ->select();

            $arr = [];
            foreach ($userRule as $row) {
                $arrtmp = explode(",", $row['rules']);
                $arr = array_merge($arr, $arrtmp);

            }

            //查找是否有具体超级管理员权限
            $key = array_search('*', $arr);

            $str = implode(',', $arr);

            //如果具体超级管理员权限，则获得所有菜单项
            if ($key === 0) {

                $menulist = Db::name('auth_rule')->alias('rule')
                    ->join('shortcutmenu menu', 'menu.auth_rule_id=rule.id and menu.admin_id=' . $this->auth->id, 'left')
                    ->field('rule.id,menu.admin_id,rule.title,rule.name,rule.ismenu,menu.is_shortcut_menu,menu.menu_color,rule.pid')
                    ->where(['rule.ismenu' => 1])
                    ->select();

            } else {

                $menulist = Db::name('auth_rule')->alias('rule')
                    ->join('shortcutmenu menu', 'menu.auth_rule_id=rule.id and menu.admin_id=' . $this->auth->id, 'left')
                    ->field('rule.id,menu.admin_id,rule.title,rule.name,rule.ismenu,menu.is_shortcut_menu,menu.menu_color,rule.pid')
                    ->where(['rule.ismenu' => 1])
                    ->where('rule.id', 'IN', $str)
                    ->select();

            }

            foreach ($menulist as $k => &$v) {
                $v['title'] = __($v['title']);
            }
            unset($v);

            //构造树形数据
            Tree::instance()->init($menulist);
            $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
            $ruledata = [0 => __('None')];
            foreach ($this->rulelist as $k => &$v) {
                if (!$v['ismenu']) {
                    continue;
                }

                $ruledata[$v['id']] = __($v['title']);//$v['title'];
            }

            $searchValue = $this->request->request("searchValue");
            $search = $this->request->request("search");

            //构造父类select列表选项数据
            $list = [];
            if ($search || $searchValue) {

                foreach ($this->rulelist as $k => &$v) {

                    if ($search && stripos($v['title'], $search) !== false) {
                        $list[] = $v;
                    }
                    if ($searchValue && in_array($v['id'], explode(',', $searchValue)) !== false) {
                        $v['title'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", " ", strip_tags($v['title'])); //过滤空格
                        $list[] = $v;
                    }
                }
            } else {
                $list = $this->rulelist;
            }

            $list = array_values($list);

            $total = count($list);

            $result = array("total" => $total, "rows" => $list);


            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 开启/关闭快捷菜单
     */
    public function multi($ids = null)
    {

        $param = input();

        $is_shortcut_menu = $param['params'];
        $is_shortcut_menu = explode("=", $is_shortcut_menu);

        $admin_id = $this->auth->id;

        $row = $this->model->get(['auth_rule_id' => $ids, 'admin_id' => $admin_id]);

        $this->modelRule = new \app\admin\model\AuthRule;

        $find = $this->modelRule->where(['ismenu' => 1, 'pid' => $ids])->select();


        if (!empty($find)) {
            $this->error('有子菜单的菜单项不支持设置快捷方式');
        }

        parse_str($this->request->post('params'), $values);

        if (empty($values)) {
            $this->error(__('You have no permission'));
        }

        if (!isset($values['menu_color'])) {
            $values['menu_color'] = "bg-red";
        }

        if (!$row) {
            $this->model->insert(['admin_id' => $admin_id, 'auth_rule_id' => $ids, 'menu_color' => $values['menu_color'], 'is_shortcut_menu' => 1]);

        } else {

            //如果是关闭快捷，则删除快捷菜单表的相应记录
            if (isset($values['is_shortcut_menu']) && $values['is_shortcut_menu'] == 0) {
                $this->model->where('id', $row['id'])->delete();
            } else {
                $this->model
                    ->where('id', $row['id'])
                    ->update($values);
            }

        }


        $this->success('操作成功');


    }


}

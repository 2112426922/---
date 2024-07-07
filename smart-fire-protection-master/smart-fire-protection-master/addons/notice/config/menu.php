<?php
/**
 * 菜单配置文件
 */

return array (
  0 => 
  array (
    'type' => 'file',
    'name' => 'notice',
    'title' => '消息管理',
    'icon' => 'fa fa-bullhorn',
    'url' => NULL,
    'condition' => '',
    'remark' => '',
    'ismenu' => 1,
    'menutype' => NULL,
    'extend' => NULL,
    'py' => 'xxgl',
    'pinyin' => 'xiaoxiguanli',
    'weigh' => 0,
    'sublist' => 
    array (
      0 => 
      array (
        'type' => 'file',
        'name' => 'notice/event',
        'title' => '消息事件',
        'icon' => 'fa fa-align-center',
        'url' => '',
        'condition' => '',
        'remark' => '',
        'ismenu' => 1,
        'menutype' => 'addtabs',
        'extend' => '',
        'py' => 'xxsj',
        'pinyin' => 'xiaoxishijian',
        'weigh' => 198,
        'sublist' => 
        array (
          0 => 
          array (
            'type' => 'file',
            'name' => 'notice/event/import',
            'title' => '导入',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'dr',
            'pinyin' => 'daoru',
            'weigh' => 0,
          ),
          1 => 
          array (
            'type' => 'file',
            'name' => 'notice/event/index',
            'title' => '查看',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'zk',
            'pinyin' => 'zhakan',
            'weigh' => 0,
          ),
          2 => 
          array (
            'type' => 'file',
            'name' => 'notice/event/recyclebin',
            'title' => '回收站',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'hsz',
            'pinyin' => 'huishouzhan',
            'weigh' => 0,
          ),
          3 => 
          array (
            'type' => 'file',
            'name' => 'notice/event/add',
            'title' => '添加',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'tj',
            'pinyin' => 'tianjia',
            'weigh' => 0,
          ),
          4 => 
          array (
            'type' => 'file',
            'name' => 'notice/event/edit',
            'title' => '编辑',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'bj',
            'pinyin' => 'bianji',
            'weigh' => 0,
          ),
          5 => 
          array (
            'type' => 'file',
            'name' => 'notice/event/del',
            'title' => '删除',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'sc',
            'pinyin' => 'shanchu',
            'weigh' => 0,
          ),
          6 => 
          array (
            'type' => 'file',
            'name' => 'notice/event/destroy',
            'title' => '真实删除',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'zssc',
            'pinyin' => 'zhenshishanchu',
            'weigh' => 0,
          ),
          7 => 
          array (
            'type' => 'file',
            'name' => 'notice/event/restore',
            'title' => '还原',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'hy',
            'pinyin' => 'huanyuan',
            'weigh' => 0,
          ),
          8 => 
          array (
            'type' => 'file',
            'name' => 'notice/event/multi',
            'title' => '批量更新',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'plgx',
            'pinyin' => 'pilianggengxin',
            'weigh' => 0,
          ),
        ),
      ),
      1 => 
      array (
        'type' => 'file',
        'name' => 'notice/template',
        'title' => '消息模版',
        'icon' => 'fa fa-connectdevelop',
        'url' => '',
        'condition' => '',
        'remark' => '',
        'ismenu' => 1,
        'menutype' => 'addtabs',
        'extend' => '',
        'py' => 'xxmb',
        'pinyin' => 'xiaoximoban',
        'weigh' => 197,
        'sublist' => 
        array (
          0 => 
          array (
            'type' => 'file',
            'name' => 'notice/template/import',
            'title' => '导入',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'dr',
            'pinyin' => 'daoru',
            'weigh' => 0,
          ),
          1 => 
          array (
            'type' => 'file',
            'name' => 'notice/template/index',
            'title' => '查看',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'zk',
            'pinyin' => 'zhakan',
            'weigh' => 0,
          ),
          2 => 
          array (
            'type' => 'file',
            'name' => 'notice/template/add',
            'title' => '添加',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'tj',
            'pinyin' => 'tianjia',
            'weigh' => 0,
          ),
          3 => 
          array (
            'type' => 'file',
            'name' => 'notice/template/edit',
            'title' => '编辑',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'bj',
            'pinyin' => 'bianji',
            'weigh' => 0,
          ),
          4 => 
          array (
            'type' => 'file',
            'name' => 'notice/template/del',
            'title' => '删除',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'sc',
            'pinyin' => 'shanchu',
            'weigh' => 0,
          ),
          5 => 
          array (
            'type' => 'file',
            'name' => 'notice/template/multi',
            'title' => '批量更新',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'plgx',
            'pinyin' => 'pilianggengxin',
            'weigh' => 0,
          ),
        ),
      ),
      2 => 
      array (
        'type' => 'file',
        'name' => 'notice/notice',
        'title' => '消息通知',
        'icon' => 'fa fa-list-ol',
        'url' => '',
        'condition' => '',
        'remark' => '',
        'ismenu' => 1,
        'menutype' => 'addtabs',
        'extend' => '',
        'py' => 'xxtz',
        'pinyin' => 'xiaoxitongzhi',
        'weigh' => 0,
        'sublist' => 
        array (
          0 => 
          array (
            'type' => 'file',
            'name' => 'notice/notice/import',
            'title' => '导入',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'dr',
            'pinyin' => 'daoru',
            'weigh' => 0,
          ),
          1 => 
          array (
            'type' => 'file',
            'name' => 'notice/notice/index',
            'title' => '查看',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'zk',
            'pinyin' => 'zhakan',
            'weigh' => 0,
          ),
          2 => 
          array (
            'type' => 'file',
            'name' => 'notice/notice/add',
            'title' => '添加',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'tj',
            'pinyin' => 'tianjia',
            'weigh' => 0,
          ),
          3 => 
          array (
            'type' => 'file',
            'name' => 'notice/notice/edit',
            'title' => '编辑',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'bj',
            'pinyin' => 'bianji',
            'weigh' => 0,
          ),
          4 => 
          array (
            'type' => 'file',
            'name' => 'notice/notice/del',
            'title' => '删除',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'sc',
            'pinyin' => 'shanchu',
            'weigh' => 0,
          ),
          5 => 
          array (
            'type' => 'file',
            'name' => 'notice/notice/multi',
            'title' => '批量更新',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'plgx',
            'pinyin' => 'pilianggengxin',
            'weigh' => 0,
          ),
        ),
      ),
      3 => 
      array (
        'type' => 'file',
        'name' => 'notice/admin',
        'title' => '我的消息',
        'icon' => 'fa fa-bullhorn',
        'url' => '',
        'condition' => '',
        'remark' => '',
        'ismenu' => 1,
        'menutype' => 'addtabs',
        'extend' => '',
        'py' => 'wdxx',
        'pinyin' => 'wodexiaoxi',
        'weigh' => 200,
        'sublist' => 
        array (
          0 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin/index',
            'title' => '查看',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'zk',
            'pinyin' => 'zhakan',
            'weigh' => 0,
          ),
        ),
      ),
      4 => 
      array (
        'type' => 'file',
        'name' => 'notice/admin_mptemplate',
        'title' => '管理员绑定微信(模版消息)',
        'icon' => 'fa fa-circle-o',
        'url' => '',
        'condition' => '',
        'remark' => '',
        'ismenu' => 1,
        'menutype' => 'addtabs',
        'extend' => '',
        'py' => 'glybdwxmbxx',
        'pinyin' => 'guanliyuanbangdingweixinmobanxiaoxi',
        'weigh' => -1,
        'sublist' => 
        array (
          0 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin_mptemplate/import',
            'title' => '导入',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'dr',
            'pinyin' => 'daoru',
            'weigh' => 0,
          ),
          1 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin_mptemplate/index',
            'title' => '查看',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'zk',
            'pinyin' => 'zhakan',
            'weigh' => 0,
          ),
          2 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin_mptemplate/recyclebin',
            'title' => '回收站',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'hsz',
            'pinyin' => 'huishouzhan',
            'weigh' => 0,
          ),
          3 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin_mptemplate/add',
            'title' => '添加',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'tj',
            'pinyin' => 'tianjia',
            'weigh' => 0,
          ),
          4 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin_mptemplate/edit',
            'title' => '编辑',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'bj',
            'pinyin' => 'bianji',
            'weigh' => 0,
          ),
          5 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin_mptemplate/del',
            'title' => '删除',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'sc',
            'pinyin' => 'shanchu',
            'weigh' => 0,
          ),
          6 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin_mptemplate/destroy',
            'title' => '真实删除',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'zssc',
            'pinyin' => 'zhenshishanchu',
            'weigh' => 0,
          ),
          7 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin_mptemplate/restore',
            'title' => '还原',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'hy',
            'pinyin' => 'huanyuan',
            'weigh' => 0,
          ),
          8 => 
          array (
            'type' => 'file',
            'name' => 'notice/admin_mptemplate/multi',
            'title' => '批量更新',
            'icon' => 'fa fa-circle-o',
            'url' => NULL,
            'condition' => '',
            'remark' => '',
            'ismenu' => 0,
            'menutype' => NULL,
            'extend' => NULL,
            'py' => 'plgx',
            'pinyin' => 'pilianggengxin',
            'weigh' => 0,
          ),
        ),
      ),
    ),
  ),
);
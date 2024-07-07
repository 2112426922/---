<?php

return array (
  0 => 
  array (
    'type' => 'radio',
    'name' => 'webscan_switch',
    'title' => '【ZR】开关',
    'value' => '1',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'tip' => 'SQL注入防防XSS拦截开关',
    'rule' => 'required',
    'extend' => '',
  ),
  1 => 
  array (
    'type' => 'string',
    'name' => 'webscan_warn',
    'title' => '【ZR】提示',
    'value' => '检测有非法攻击代码，请停止攻击，否则将加入黑名单，如有疑问请联系我们',
    'content' => '',
    'tip' => '',
    'rule' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'type' => 'text',
    'name' => 'webscan_white_url',
    'title' => '【ZR】放行url',
    'value' => 'admin|index.php/admin',
    'content' => '',
    'tip' => '多个以|隔开，前置优先原则，如输入admin,admin开头的所有访问连接都不拦截',
    'rule' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'type' => 'radio',
    'name' => 'ccopen',
    'title' => '【CC】攻击开启',
    'value' => '1',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'tip' => 'CC攻击拦截开关',
    'rule' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'type' => 'text',
    'name' => 'white_url',
    'title' => '【CC】放行URL',
    'value' => 'index.php?s=/captcha|addons/webscan|captcha|index.php/addons/webscan',
    'content' => '',
    'tip' => '多个以|隔开，前置优先原则，如输入admin,admin开头的所有访问连接都不拦截',
    'rule' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'type' => 'number',
    'name' => 'seconds',
    'title' => '【CC】秒数',
    'value' => '60',
    'content' => '',
    'tip' => '规定时间内配合下面的访问次数触发CC',
    'rule' => '',
    'extend' => '',
  ),
  6 => 
  array (
    'type' => 'number',
    'name' => 'refresh',
    'title' => '【CC】访问次数',
    'value' => '100',
    'content' => '',
    'tip' => '在上面的时间内访问次数触发CC',
    'rule' => '',
    'extend' => '',
  ),
  7 => 
  array (
    'type' => 'string',
    'name' => 'return_json',
    'title' => '需返回json的目录',
    'value' => 'api',
    'content' => '',
    'tip' => '报错需要返回json的接口目录，多个以|隔开 ，如api或api|admin/index/login',
    'rule' => '',
    'extend' => '',
  ),
  8 => 
  array (
    'type' => 'text',
    'name' => 'webscan_white_ip',
    'title' => '白名单ip',
    'value' => '127.0.0.1',
    'content' => '',
    'tip' => '//白名单ip 一行一条记录 如：127.0.0.1',
    'rule' => '',
    'extend' => '',
  ),
  9 => 
  array (
    'type' => 'text',
    'name' => 'webscan_black_ip',
    'title' => '黑名单IP',
    'value' => '',
    'content' => '',
    'tip' => ' 一行一条记录',
    'rule' => '',
    'extend' => '',
  ),
  10 => 
  array (
    'type' => 'string',
    'name' => 'black_warn',
    'title' => '黑名单提示',
    'value' => '您已被加入黑名单，如有疑问请联系我们',
    'content' => '',
    'tip' => '黑名单IP访问时提示',
    'rule' => '',
    'extend' => '',
  ),
  11 => 
  array (
    'type' => 'number',
    'name' => 'black_auto',
    'title' => '自动黑名单',
    'value' => '0',
    'content' => '',
    'tip' => '攻击多少次/天，自动加入黑名单。为0不加入黑名单',
    'rule' => '',
    'extend' => '',
  ),
  12 => 
  array (
    'type' => 'string',
    'name' => 'files_suffix',
    'title' => '【校验】文件后缀',
    'value' => 'php|js|css|html',
    'content' => '',
    'tip' => '需要校验的文件后缀，|隔开',
    'rule' => '',
    'extend' => '',
  ),
  13 => 
  array (
    'type' => 'string',
    'name' => 'ignore_dir',
    'title' => '【校验】忽略文件',
    'value' => '.idea|.settings|.svn|runtime',
    'content' => '',
    'tip' => '忽略文件或文件夹校验，|隔开',
    'rule' => '',
    'extend' => '',
  ),
);

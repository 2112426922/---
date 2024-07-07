<?php

return array(
    array(
        'name'    => 'mode',
        'title'   => '类型',
        'type'    => 'checkbox',
        'content' =>
            array(
                'gallery' => '图像',
                'text'    => '随机文字',
                'custom'  => '自定义文字',
            ),
        'value'   => 'gallery,text,custom',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '用于前台显示点选的类型',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'background',
        'title'   => '背景图',
        'type'    => 'images',
        'content' =>
            array(),
        'value'   => '/assets/addons/clicaptcha/img/1.png,/assets/addons/clicaptcha/img/2.png,/assets/addons/clicaptcha/img/3.png',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '验证码背景图，不少于3张图片，建议长宽为350*233，目前仅支持gif,jpeg,png三种格式',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'font',
        'title'   => '字体',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => '/assets/fonts/SourceHanSansK-Regular.ttf',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '验证码字体',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'customtext',
        'title'   => '自定义验证文字',
        'type'    => 'text',
        'content' =>
            array(),
        'value'   => '',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '一行一个词语<br>建议不超过6个汉字',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'textsize',
        'title'   => '点选文字(图像)数量',
        'type'    => 'number',
        'content' =>
            array(),
        'value'   => '4',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '点选的文字或图像的数量，建议3-5个',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'disturbsize',
        'title'   => '干扰文字(图像)数量',
        'type'    => 'number',
        'content' =>
            array(),
        'value'   => '2',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '干扰文字或图像的数量',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'alpha',
        'title'   => '验证码透明度',
        'type'    => 'number',
        'content' =>
            array(),
        'value'   => '30',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '验证码透明度，0-100，0表示透明,100表示不透明',
        'ok'      => '',
        'extend'  => '',
    ),
);

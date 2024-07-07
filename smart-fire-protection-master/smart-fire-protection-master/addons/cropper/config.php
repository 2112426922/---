<?php

return [
    [
        'name'    => 'dialogWidth',
        'title'   => '弹窗宽度',
        'type'    => 'number',
        'content' => [
        ],
        'value'   => '800',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '单位为px，可视窗口高度小于该值时自适应',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'dialogHeight',
        'title'   => '弹窗高度',
        'type'    => 'number',
        'content' => [
        ],
        'value'   => '600',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '单位为px，可视窗口宽度小于该值时自适应',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'containerMinHeight',
        'title'   => '容器最小高度',
        'type'    => 'number',
        'content' => [
        ],
        'value'   => '200',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'containerMaxHeight',
        'title'   => '容器最大高度',
        'type'    => 'number',
        'content' => [
        ],
        'value'   => '800',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'customWidthHeight',
        'title'   => '自定义宽度和高度',
        'type'    => 'radio',
        'content' => [
            1 => '开',
            0 => '关',
        ],
        'value'   => '1',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '是否开启手动设置宽度和高度，需关闭固定剪裁比例',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'customAspectRatio',
        'title'   => '自定义剪裁比例',
        'type'    => 'radio',
        'content' => [
            1 => '开',
            0 => '关',
        ],
        'value'   => '1',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '是否开启自定义剪裁比例选项，需关闭固定剪裁比例',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'aspectRatio',
        'title'   => '固定剪裁比例',
        'type'    => 'number',
        'content' => [],
        'value'   => '0',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '宽:高=比例值，16:9=1.777777 4:3=1.333333',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'cropBoxMovable',
        'title'   => '是否可移动图像',
        'type'    => 'radio',
        'content' => [
            1 => '是',
            0 => '否',
        ],
        'value'   => '1',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'cropBoxResizable',
        'title'   => '是否允许调整裁剪框的大小',
        'type'    => 'radio',
        'content' => [
            1 => '是',
            0 => '否',
        ],
        'value'   => '1',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'minCropBoxWidth',
        'title'   => '剪切框宽度最小值',
        'type'    => 'number',
        'content' => [],
        'value'   => '0',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'minCropBoxHeight',
        'title'   => '剪切框高度最小值',
        'type'    => 'number',
        'content' => [],
        'value'   => '0',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'minContainerWidth',
        'title'   => '容器宽度最小值',
        'type'    => 'number',
        'content' => [],
        'value'   => '200',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'minContainerHeight',
        'title'   => '容器高度最小值',
        'type'    => 'number',
        'content' => [],
        'value'   => '100',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'minCanvasWidth',
        'title'   => '画布宽度最小值',
        'type'    => 'number',
        'content' => [],
        'value'   => '0',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '单位为px',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'minCanvasHeight',
        'title'   => '画布高度最小值',
        'type'    => 'number',
        'content' => [],
        'value'   => '0',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'autoCropArea',
        'title'   => '自动剪裁区域的大小比例',
        'type'    => 'number',
        'content' => [],
        'value'   => '0.8',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'croppedWidth',
        'title'   => '默认宽度',
        'type'    => 'number',
        'content' => [],
        'value'   => '0',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '单位为px',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'croppedHeight',
        'title'   => '默认高度',
        'type'    => 'number',
        'content' => [],
        'value'   => '0',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '单位为px',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'croppedMinWidth',
        'title'   => '默认输出最小宽度',
        'type'    => 'number',
        'content' => [],
        'value'   => '0',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '单位为px',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'croppedMinHeight',
        'title'   => '默认输出最小高度',
        'type'    => 'number',
        'content' => [],
        'value'   => '0',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'croppedMaxWidth',
        'title'   => '默认输出最大宽度',
        'type'    => 'number',
        'content' => [],
        'value'   => '',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '默认为无限制，单位为px',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'croppedMaxHeight',
        'title'   => '默认输出最大高度',
        'type'    => 'number',
        'content' => [],
        'value'   => '',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '默认为无限制，单位为px',
        'ok'      => '',
        'extend'  => '',
    ],
    [
        'name'    => 'fillColor',
        'title'   => '填充颜色',
        'type'    => 'string',
        'content' => [],
        'value'   => '',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '默认为透明',
        'ok'      => '',
        'extend'  => '',
    ],
];

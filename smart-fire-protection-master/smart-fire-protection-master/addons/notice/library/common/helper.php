<?php

require_once 'extend.php';

if (!function_exists('return_error')) {

    class HttpResponseException extends \think\exception\HttpResponseException
    {
        public function __construct(\think\Response $response)
        {
            $this->response = $response;
            $this->message = $response->getData()['msg'];
        }
    }

    /**
     * 返回错误信息
     */
    function return_error($message, $code = 0, $data = null)
    {
        $data = [
            'code' => $code,
            'msg' => $message,
            'data' => $data,
            'time' => time(),
        ];
        $response = \think\Response::create($data, 'json', 200);
        throw new HttpResponseException($response);
    }
};


if (!function_exists('array_only')) {
    /**
     * 获取数组指定建值
     * @param $arr
     * @param $keys
     *
     * @return array
     */
    function array_only($arr, $keys) {
        if ($keys == '*') {
            return $arr;
        }
        $val = [];
        foreach ($keys as $key) {
            if (isset($arr[$key])) {
                $val[$key] = $arr[$key];
            }
        }
        return $val;
    }
}


if (!function_exists('row_check')) {
    /**
     * 数据检测
     */
    function row_check($row, $author = false) {
        if (!$row) {
            return_error('数据不存在');
        }
        if (isset($row['visible_switch'])) {
            if ($row['visible_switch'] == 0) {
                return_error('数据已被删除');
            }
        }
        if ($author) {
            $field = is_string($author) ? $author : 'user_id';
            if ($row[$field] != (\app\common\library\Auth::instance())->id) {
                return_error('越权操作');
            }
        }
        return true;
    }
}


if (!function_exists('time_text')) {
    /**
     * 将时间戳转换为日期时间文本格式
     *
     * @param int    $time   时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function time_text($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        if ($time == 0) {
            return '无';
        }
        return date($format, $time);
    }
}
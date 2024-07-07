<?php

namespace addons\adminlogin\library;


use think\exception\HttpResponseException;
use think\Request;
use think\Response;
use think\response\Redirect;
use think\Session;

class Service
{

    public static function getUrl()
    {
        $url = \request()->get('url', 'index/index', 'url_clean');
        // 返回地址不能是 login/logout
        $arr = [
            'index/login',
            'index/logout',
            'adminlogin/index'
        ];
        foreach ($arr as $item) {
            if ($url == url($item)) {
                $url = 'index/index';
                break;
            }
        }

        return $url;
    }

    /**
     * URL 重定向
     * @access protected
     * @param string    $url    跳转的 URL 表达式
     * @param array|int $params 其它 URL 参数
     * @param int       $code   http code
     * @param array     $with   隐式传参
     * @return void
     * @throws HttpResponseException
     */
    public static function redirect($url='', $params = [], $code = 302, $with = [])
    {
        $url = 'adminlogin/index';
        if (empty($params)) {
            $goUrl = Session::get('referer');
            $goUrl = $goUrl ? $goUrl : request()->url();
            $params['url'] = $goUrl;
        }

        if (is_integer($params)) {
            $code   = $params;
            $params = [];
        }

        $response = new Redirect($url);
        $response->code($code)->params($params)->with($with);

        throw new HttpResponseException($response);
        die;
    }


    /**
     * 返回错误消息
     */
    public static function error()
    {
        if (request()->isAjax()) {
            $data = [
                'code' => 0,
                'msg' => __('Please login first'),
                'data' => [],
                'time' => time(),
            ];
            $response = \think\Response::create($data, 'json', 200);
            throw new HttpResponseException($response);
        } else {
            static::redirect();
        }
    }
}

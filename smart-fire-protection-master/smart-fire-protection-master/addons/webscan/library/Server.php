<?php

namespace addons\webscan\library;

use addons\webscan\model\WebscanLog;
use think\Config;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;
use think\Url;

/**
 * Class server
 * @package addons\webscan\library
 */
abstract class Server
{
    /**
     * 错误信息
     * @var
     */
    protected $error;

    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * url白名单
     * @param $white_url
     * @return bool
     */
    protected function whiteUrl($white_url, $url_var = '')
    {
        if (!$white_url) return false;

        $url_var = $url_var ?: isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
        $url_var = strpos($url_var, '/') != 0 ?: substr($url_var, 1);
        $search = ["/", "?", "=", ".", "&", '|'];
        $replace = ["\/", "\?", "\=", "\.", "\&", '|^'];
        $white_url = str_replace($search, $replace, $white_url);

        if (preg_match("/^" . $white_url . "/is", $url_var)) {
            return true;
        }

        return false;
    }

    /**
     * ip白名单
     * @param $white_ip
     * @param string $ip
     * @return bool
     */
    protected function whiteIp($white_ip, $ip = '')
    {
        $ip = $ip ?: \request()->ip();

        if ($ip && $white_ip) {
            $webscan_white_ip_arr = explode(PHP_EOL, $white_ip);
            if (count($webscan_white_ip_arr) > 0) {
                if (in_array($ip, $webscan_white_ip_arr)) {
                    return true;
                }
            }

        }

        return false;
    }

    /**
     *  防护提示
     */
    protected function result($msg, $data = null, $code = 0, $type = null, array $header = [])
    {
        $url = '';

        if (is_null($url)) {
            $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url && !strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = Url::build($url);
        }

        $type = $type ?: $this->getResponseType();
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => [],
            'url' => $url,
        ];

        if ('html' == strtolower($type)) {
            $template = Config::get('template');
            $view = Config::get('view_replace_str');
            $result = \think\View::instance($template, $view)->fetch(Config::get('dispatch_error_tmpl'), $result);
        }

        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        return Request::instance()->isAjax()
            ? Config::get('default_ajax_return')
            : Config::get('default_return_type');
    }

    /**
     *  日记记录
     */
    protected function webscanSlog($logs)
    {
        WebscanLog::create($logs);

        if ($this->config['black_auto'] > 0) {
            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            if ((new WebscanLog())->where('ip', $logs['ip'])->where('create_time', '>', $beginToday)->count() >= $this->config['black_auto']) {
                //加入黑名单
                $config = get_addon_config('webscan');
                //更新配置文件
                $config['webscan_black_ip'] = $config['webscan_black_ip'] . PHP_EOL . $logs['ip'];
                set_addon_config('webscan', $config);
                \think\addons\Service::refresh();
            }

        }

    }
}

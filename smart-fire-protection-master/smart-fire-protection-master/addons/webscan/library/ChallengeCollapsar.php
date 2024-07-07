<?php

namespace addons\webscan\library;

use addons\webscan\model\WebscanLog;
use think\Cache;
use think\Validate;

/**
 * CC攻击助手
 * @author amplam 122795200@qq.com
 * @date 2019年10月30日 16:21:52
 */
class ChallengeCollapsar extends Server
{
    private $cachename = 'ChallengeCollapsar';
    protected $config = [
        'seconds' => 60,//多少秒以内
        'refresh' => 60,//刷新、访问次数
        'white_url' => "",
    ];

    /**
     * 构造函数
     * WxPay constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * CC攻击防护开始
     * @return bool
     */
    public function start()
    {
        //CC攻击URL白名单
        if ($this->whiteUrl($this->config['white_url'])) return true;
        //CC攻击URL白名单

        //ip白名单
        if ($this->whiteIp($this->config['webscan_white_ip'])) return true;

        $now_time = time();
        $ip = request()->ip();
        $data = Cache::get($this->cachename . md5($ip));

        if ($data) {
            $data['refresh_times'] = $data['refresh_times'] + 1;
        } else {
            $data['refresh_times'] = 1;
            $data['last_time'] = $now_time;
        }

        if (($now_time - $data['last_time']) < $this->config['seconds']) {

            if ($data['refresh_times'] >= $this->config['refresh']) {
                $captcha = request()->param('captcha');
                if (!$captcha) {
                    //保存访问日志 相等才保存，不然可能会很多日志
                    if ($data['refresh_times'] == $this->config['refresh']) {
                        $logs = array('ip' => $ip, 'page' => $_SERVER["PHP_SELF"], 'method' => request()->method(), 'rkey' => "CC攻击", 'rdata' => '', 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'request_url' => $_SERVER["REQUEST_URI"], 'type' => 'cc');
                        WebscanLog::create($logs);
                        Cache::set($this->cachename . md5($ip), $data, 3600);
                    }

                    if ($this->config['return_json']) {
                        $this->config['return_json'] = str_replace("/", "\\/", $this->config['return_json']);
                        if (preg_match("/^" . $this->config['return_json'] . "/is", request()->pathinfo())) {
                            return $this->result("请输入验证码", [], '-1101', 'json');
                        }

                    }

                    if ($this->getResponseType() !== 'html') {
                        return $this->result("请输入验证码", [], '-1101', $this->getResponseType());
                    }

                    header('Location: ' . addon_url('webscan/index/index', ['from' => $_SERVER['REQUEST_URI']]));//跳转到输入验证码界面
                    exit;
                }

                $rule['captcha'] = 'require|captcha';
                $validate = new Validate($rule, [], ['captcha' => "验证码"]);
                $result = $validate->check(['captcha' => $captcha]);

                if (!$result) {
                    if ($this->config['return_json']) {
                        $this->config['return_json'] = str_replace("/", "\\/", $this->config['return_json']);
                        if (preg_match("/^" . $this->config['return_json'] . "/is", request()->pathinfo())) {
                            return $this->result("验证码错误", [], '-1102', 'json');
                        }

                    }

                    if ($this->getResponseType() !== 'html') {
                        return $this->result("验证码错误", [], '-1102', $this->getResponseType());
                    }

                    header('Location:' . addon_url('webscan/index/index', ['from' => $_SERVER['REQUEST_URI']]));//跳转到输入验证码界面
                    exit();
                }

                $data['refresh_times'] = 1;
                $data['last_time'] = $now_time;
            }

        } else {
            $data['refresh_times'] = 1;
            $data['last_time'] = $now_time;
        }

        Cache::set($this->cachename . md5($ip), $data, 3600);

        return true;
    }
}
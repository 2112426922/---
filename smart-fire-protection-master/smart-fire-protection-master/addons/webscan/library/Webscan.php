<?php

namespace addons\webscan\library;

use addons\webscan\model\WebscanLog;
use think\Config;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;
use think\Url;

/**
 * SQL注入XSS攻击助手
 * @author amplam 122795200@qq.com
 * @date 2019年10月29日 17:43:27
 */
class Webscan extends Server
{
    protected $error = "";
    //get拦截规则
    private $getfilter = "\\<.+javascript:window\\[.{1}\\\\x|<.*=(&#\\d+?;?)+?>|<.*(data|src)=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    //post拦截规则
    private $postfilter = "<.*=(&#\\d+?;?)+?>|<.*data=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    //cookie拦截规则
    private $cookiefilter = "benchmark\s*?\(.*\)|sleep\s*?\(.*\)|load_file\s*?\\(|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

    protected $config = [
        'webscan_switch' => 1,//拦截开关(1为开启，0关闭)
        //提交方式拦截(1开启拦截,0关闭拦截,post,get,cookie,referre选择需要拦截的方式)
        'webscan_post' => 1,
        'webscan_get' => 1,
        'webscan_cookie' => 1,
        'webscan_referre' => 1,
        'black_auto' => 0,//攻击多少次/天，自动加入黑名单。为0不加入黑名单
        'webscan_warn' => "检测有非法攻击代码，请停止攻击，否则将加入黑名单，如有疑问请联系我们",//提示
        'webscan_white_module' => '',//放行指定模块 多个以|隔开 如：admin|admin1
        'webscan_white_url' => '',//模块/控制器/方法 一行一条记录 如：admin/index/login
        'webscan_white_ip' => '',//白名单ip 一行一条记录 如：127.0.0.1
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
     * 开始拦截
     */
    public function start()
    {

        if ($this->config['webscan_switch'] && $this->webscanWhite($this->config['webscan_white_module'], $this->config['webscan_white_url'])) {

            if ($this->config['webscan_get']) {
                $_GET['temp_url_path'] = \request()->pathinfo();//请求的路径也加入检测
                foreach ($_GET as $key => $value) {
                    $this->webscanStopAttack($key, $value, $this->getfilter, "GET");
                }
            }

            if ($this->config['webscan_post']) {
                foreach ($_POST as $key => $value) {
                    $this->webscanStopAttack($key, $value, $this->postfilter, "POST");
                }
            }

            if ($this->config['webscan_cookie']) {
                foreach ($_COOKIE as $key => $value) {
                    $this->webscanStopAttack($key, $value, $this->cookiefilter, "COOKIE");
                }
            }

            if ($this->config['webscan_referre']) {
                //referer获取
                $webscan_referer = empty($_SERVER['HTTP_REFERER']) ? array() : array('HTTP_REFERER' => $_SERVER['HTTP_REFERER']);
                foreach ($webscan_referer as $key => $value) {
                    $this->webscanStopAttack($key, $value, $this->postfilter, "REFERRER");
                }
            }

            //其他类似put,delete的检测；
            $method = \request()->method(true);

            // 自动获取请求变量
            switch ($method) {
                case 'PUT':
                case 'DELETE':
                case 'PATCH':
                    $put_arr = \request()->put();
                    foreach ($put_arr as $key => $value) {
                        $this->webscanStopAttack($key, $value, $this->postfilter, $method);
                    }
                    break;
                default:
            }

        }
    }

    /**
     *  拦截白名单
     * true需要拦截，false 不需要
     */
    private function webscanWhite($webscan_white_module, $webscan_white_url)
    {
        //ip白名单
        if ($this->whiteIp($this->config['webscan_white_ip'])) return false;

        //URL白名单
        if ($this->whiteUrl($webscan_white_url)) return false;

        return true;
    }


    /**
     *  攻击检查拦截
     */
    private function webscanStopAttack($StrFiltKey, $StrFiltValue, $ArrFiltReq, $method)
    {
        $StrFiltValue = $this->webscanArrForeach($StrFiltValue);

        if (preg_match("/" . $ArrFiltReq . "/is", $StrFiltValue) == 1) {
            $this->webscanSlog(array('ip' => \request()->ip(), 'page' => $_SERVER["PHP_SELF"], 'method' => \request()->method(), 'rkey' => $StrFiltKey, 'rdata' => $StrFiltValue, 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'request_url' => $_SERVER["REQUEST_URI"], 'type' => 'webscan'));
            return $this->webscanPape();
        }

        if (preg_match("/" . $ArrFiltReq . "/is", $StrFiltKey) == 1) {
            $this->webscanSlog(array('ip' => \request()->ip(), 'page' => $_SERVER["PHP_SELF"], 'method' => \request()->method(), 'rkey' => $StrFiltKey, 'rdata' => $StrFiltKey, 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'request_url' => $_SERVER["REQUEST_URI"], 'type' => 'webscan'));
            return $this->webscanPape();
        }

    }

    /**
     *  参数拆分
     */
    private function webscanArrForeach($arr)
    {
        static $str;
        static $keystr;

        if (!is_array($arr)) {
            return $arr;
        }

        foreach ($arr as $key => $val) {
            $keystr = $keystr . $key;
            if (is_array($val)) {
                return $this->webscanArrForeach($val);
            } else {
                $str[] = $val . $keystr;
            }
        }

        return implode($str);
    }

    /**
     *  防护提示
     */
    public function webscanPape()
    {

        if ($this->config['return_json']) {
            $url_var = $_SERVER['REQUEST_URI'];
            $url_var = strpos($url_var, '/') != 0 ?: substr($url_var, 1);
            $search = ["/", "?", "=", ".", "&", '|'];
            $replace = ["\/", "\?", "\=", "\.", "\&", '|^'];
            $this->config['return_json'] = str_replace($search, $replace, $this->config['return_json']);
            if (preg_match("/^" . $this->config['return_json'] . "/is", $url_var)) {
                return $this->result($this->config['webscan_warn'], [], '-110', 'json');
            }

        }

        return $this->result($this->config['webscan_warn'], [], '-110', $this->getResponseType());
    }


}

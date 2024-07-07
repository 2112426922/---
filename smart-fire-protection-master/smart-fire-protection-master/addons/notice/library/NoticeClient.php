<?php

/**
 *  QQ: 1123416584
 */
namespace addons\notice\library;

use addons\notice\library\server\Msg;
use app\admin\model\notice\Notice;
use app\admin\model\notice\NoticeEvent;
use app\admin\model\notice\NoticeTemplate;
use hnh\custom\Log;

/**
 *
 * @package addons\notice\library
 */
class NoticeClient
{

    /**
     * @var object 对象实例
     */
    protected static $instance;

    // 所有的模版
    public $templateList = [];

    /**
     * 通知服务提供者
     * @var array
     */
    private $providers = [
        'msg'      => 'Msg',
        'email'   => 'Email',
        'mptemplate' => 'Mptemplate',
        'miniapp' => 'Miniapp',
        'sms' => 'Sms',
    ];

    /**
     * 服务对象信息
     * @var array
     */
    protected $services = [];

    // 插件配置
    public $addonConfig = [];

    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return NoticeClient
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }


    public function __construct()
    {
        // 加载模板
        $this->templateList = NoticeTemplate::select();
        $this->registerProviders();
        $this->addonConfig = get_addon_config('notice');
    }

    /**
     * 发送消息通知
     *
     * @param $event
     * @param $params 参数必须和后台配置的完全一致
     *
     * @throws \think\exception\DbException
     */
    public function trigger($event, $params)
    {
        // 查询事件
        $event = NoticeEvent::get(['event' => $event]);
        if (!$event || $event['visible_switch'] == 0) {
            $this->error = '事件不存在';
            return false;
        }

        // 判断字段是否按规定传对了
        $needFields = json_decode($event['content'], true);
        $diff = array_diff_key($needFields, $params);
        if (count($diff) > 0) {
            // 记录日志
            $logData = [
                'name' =>  $event['name'].'-缺少参数',
                'event' => $event->toArray(),
                'params' => $params,
                '缺少参数' => $diff
            ];
            Log::error($logData);

            // 发送失败次数
            $event->setInc('send_fail_num');
            $this->error = $event['name'].'-缺少参数';
            return false;
        }


        $platformArr = explode(',', $event['platform']);
        $typeArr = explode(',', $event['type']);
        $platformData = $this->getPlatformData();

        // 所有可发送消息的模板
        $templateList = array_filter($this->templateList,
            function ($item) use ($event, $platformArr, $typeArr, $platformData) {
                if ($item['notice_event_id'] != $event['id']) {
                    return false;
                }
                if (!in_array($item['platform'], $platformArr)) {
                    return false;
                }
                if (!in_array($item['type'], $typeArr)) {
                    return false;
                }
                if (!isset($platformData[$item['platform']])) {
                    return false;
                }
                if (!in_array($item['type'],$platformData[$item['platform']]['type'])) {
                    return false;
                }
                if ($item['visible_switch'] == 0) {
                    return false;
                }
                return true;
            }
        );

        foreach ($templateList as $item) {
            try{
                // 发送次数
                $event->setInc('send_num');
                $item->setInc('send_num');

                // 添加消息记录
                $noticeData = [
                    'name' => $event['name'],
                    'event' => $event['event'],
                    'platform' => $item['platform'],
                    'type' => $item['type'],
                    'to_id' => 0,
                    'content' => '',
                    'ext' => '',
                    'notice_template_id' => $item['id']
                ];
                $getNoticeData = $this->services[$item['type']]->getNoticeData($event, $item, $params);
                $noticeData = array_merge($noticeData, $getNoticeData);

                // 批量发送情况
                $to_id = is_array($noticeData['to_id']) ? $noticeData['to_id'] : [$noticeData['to_id']];
                foreach ($to_id as $v2) {
                    $noticeData['to_id'] = $v2;
                    Notice::create($noticeData);
                }
            }catch (\Exception $e) {
                // 记录日志
                $logData = [
                    'notice_template_id' => $item['id'],
                    'name' =>  $event['name'],
                    'event' => $event->toArray(),
                    'template' => $item->toArray(),
                    'params' => $params
                ];
                Log::catch('模板发送失败', $e, $logData);

                // 发送失败次数
                $this->error = $e->getMessage();
                $event->setInc('send_fail_num');
                $item->setInc('send_fail_num');
            }
        }

        return true;
    }

    // 获取配置
    public function getPlatformData()
    {
        $typeList = $this->getTypeList();
        $platformList = $this->getPlatformList();

        /*$list = [
            'user' => [
                'name' => '用户',
                'type' => ['msg','email'],
            ],
            'admin' => [
                'name' => '后台',
                'type' => ['msg','email'],
            ],
        ];*/

        $list = [];
        foreach ($platformList as $k=>$v) {
            $type = $this->addonConfig['open'][$k] ?? '';
            $type =  explode(',', $type);
            $type = array_combine($type, $type);
            $type = array_intersect_key($type, $typeList);
            $listItem = [
                'name' => $v,
                'type' => $type,
            ];
            $list[$k] = $listItem;
        }

        return $list;
    }

    // 获取类型中文名称
    public function getTypeText($type)
    {
        return $this->getTypeList()[$type]?:'未知';
    }

    // 所有平台
    public function getPlatformList()
    {
        $all =  ['user' => '用户', 'admin' => '后台'];
        $platform = $this->addonConfig['platform'];
        $platform = explode(',', $platform);

        $list = [];
        foreach ($all as $k => $v) {
            if (in_array($k, $platform)) {
                $list[$k] = $v;
            }
        }

        return $list;
    }

    // 所有类型
    public function getTypeList()
    {
        $all =  (new NoticeEvent())->getTypeList();
        $platform = $this->addonConfig['type'];
        $platform = explode(',', $platform);

        $list = [];
        foreach ($all as $k => $v) {
            if (in_array($k, $platform)) {
                $list[$k] = $v;
            }
        }

        return $list;
    }

    // 根据平台和类型获取模板
    public function getTemplateByPlatformAndType($notice_event_id,$platform, $type)
    {
        $list = $this->templateList;
        foreach ($list as $item) {
            if ($item['platform'] == $platform && $item['type'] == $type && $item['notice_event_id'] == $notice_event_id) {
                return $item;
            }
        }
        return false;
    }

    /**
     * 错误信息
     * @var null
     */
    public $error = null;

    /**
     * 获取错误信息
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 注册服务提供者
     */
    private function registerProviders()
    {
        foreach ($this->providers as $k => $v) {
            $objname = __NAMESPACE__ . "\\server\\{$v}";
            $this->services[$k] = new $objname();
        }
    }
}
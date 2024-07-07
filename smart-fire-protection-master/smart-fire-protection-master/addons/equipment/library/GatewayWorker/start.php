<?php
namespace addons\equipment\library\gatewayworker;
ini_set('display_errors', 'on');
use Workerman\Worker;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class start extends Command
{

    protected function configure()
    {
        $this->setName('equipment')
            ->addArgument('action', Argument::OPTIONAL, "action  start [d]|stop|restart|status")
            ->addArgument('type', Argument::OPTIONAL, "d -d")
            ->setDescription('equipment 信息服务');
    }
    protected function execute(Input $input, Output $output)
    {
        global $argv;
        $action = trim($input->getArgument('action'));
        $type   = trim($input->getArgument('type')) ? '-d' : '';
        $argv[0] = 'notice';
        $argv[1] = $action;
        $argv[2] = $type ? '-d' : '';
        $this->start();
    }
    private function start()
    {
        if (strpos(strtolower(PHP_OS), 'win') === 0) {
            exit("Windows下不支持窗口启动，请手动运行(not support windows, please use)：addons/equipment/library/gatewayworker/start_for_win.bat\n");
        }
        // 检查扩展
        if (!extension_loaded('pcntl')) {
            exit("Please install pcntl extension. See http://doc.workerman.net/appendices/install-extension.html\n");
        }
        if (!extension_loaded('posix')) {
            exit("Please install posix extension. See http://doc.workerman.net/appendices/install-extension.html\n");
        }
        // 标记是全局启动
        define('GLOBAL_START', 1);
        require_once __DIR__ . '/vendor/autoload.php';
        // 加载所有Applications/*/start.php，以便启动所有服务
        foreach (glob(__DIR__ . '/Applications/*/start*.php') as $start_file) {
            require_once $start_file;
        }
        // 运行所有服务
        Worker::runAll();
    }
}

<?php

namespace app\admin\controller\screen;

use app\common\controller\Backend;
//b***高级授权***//
use \ZipArchive;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
//e***高级授权***//

/**
 * 大屏管理
 *
 * @icon fa fa-circle-o
 */
class Page extends Backend
{

    /**
     * Page模型对象
     * @var \app\common\model\screen\Page
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\screen\Page;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    //b***高级授权***//
    protected static $exportSaveDir = '';

    // 导出
    public function export()
    {
        $search = $this->request->post('search');
        $ids = $this->request->post('ids');
        $filter = $this->request->post('filter');
        $op = $this->request->post('op');

        $idpk = 'id';
        $whereIds = $ids == 'all' ? '1=1' : [$idpk => ['in', explode(',', $ids)]];
        $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
        $this->relationSearch = true;

        list($where, $sort, $order, $offset, $limit) = $this->buildparams();

        $query = $this->model
            ->where($whereIds)
            ->where($where);
        $list = $query->select();
        if (count($list) == 0) {
            $this->error('数据不能为空');
        }

        static::$exportSaveDir = RUNTIME_PATH.'addon_screen/export/'.date('y-m-d H:i:s');

        $data = [
            'title' => [],
            'time' => time(),
            'ip' => request()->ip(),
            'useragent' => substr(request()->server('HTTP_USER_AGENT'), 0, 255),
            'list' => [],
        ];

        foreach ($list as $row) {
            $content = json_decode($row['content'], true);

            if (isset($content['dashboard']) && isset($content['dashboard']['backgroundImage'])) {
                $content['dashboard']['backgroundImage'] = $this->exportSaveImage('backgroundImage', $content['dashboard']['backgroundImage']);
            }

            if (isset($content['widgets'])) {
                foreach ($content['widgets'] as &$widget) {
                    if (isset($widget['value']) && isset($widget['value']['setup'])) {
                        foreach ($widget['value']['setup'] as $key=>$val) {
                            $widget['value']['setup'][$key] = $this->exportSaveImage($key, $val);
                        }
                    }
                }
                unset($widget);
            }

            $row['content'] = json_encode($content);
            $data['list'][] = $row->toArray();
            $data['title'][] = $row['title'];
        }

        $data['title'] = implode(',', $data['title']);
        $content = json_encode($data, true);

        $savePath = static::$exportSaveDir.'/data.json';
        $directory = dirname($savePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        file_put_contents($savePath, $content);

        $this->zipFolder(static::$exportSaveDir, static::$exportSaveDir.'/screen.zip');

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $data['title'] . '.zip"');
        header('Content-Length: ' . filesize(static::$exportSaveDir.'/screen.zip'));
        readfile(static::$exportSaveDir.'/screen.zip');
    }

    // 压缩zip
    protected function zipFolder($folderPath, $zipPath) {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($folderPath) + 1);
                $relativePath = implode('/', array_slice(explode('/', $relativePath), 4));
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return true;
    }

    // 保存图片到本地
    protected function exportSaveImage($key, $val)
    {
        if (false == ((stripos($key, 'image') !== false || stripos($key, 'img') !== false))) {
            return $val;
        }
        if (!$val) {
            return $val;
        }
        if (!is_string($val)) {
            return $val;
        }

        $imageUrl = cdnurl($val, true);
        $urlArr = parse_url($imageUrl);
        $saveDir = static::$exportSaveDir;

        if (empty($urlArr['path'])) {
            return false;
        }

        // 保存图片到压缩包文件夹
        (function () use ($urlArr, $imageUrl, $saveDir) {

            $savePath = $saveDir.'/images'.$urlArr['path'];
            $directory = dirname($savePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // 本地图片
            if (file_exists('./'.$urlArr['path'])) {
                copy('./'.$urlArr['path'], $savePath);
                return true;
            }

            stream_context_set_default( [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            // 是否为网络图片
            $headers = @get_headers($imageUrl);
            if (false == $headers || strpos($headers[0], '200') === false) {
                return false;
            }

            $this->exportSaveRemoteImage($imageUrl, $savePath);
        })();

        $path = $urlArr['path'];
        $imageUrl = $path;
        return $imageUrl;
    }

    // 下载网络图片
    protected function exportSaveRemoteImage($url, $savePath) {

        $imageData = file_get_contents($url);
        if ($imageData === false) {
            return false;
        }

        $result = file_put_contents($savePath, $imageData);
        if ($result === false) {
            return false;
        }

        return true;
    }

    // 导入
    public function import()
    {
        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH  . 'public' . DS . $file;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }

        $extractTo = RUNTIME_PATH.'addon_screen/import/'.date('y-m-d H:i:s');
        $directory = dirname($extractTo);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($filePath) === TRUE) {
            $zip->extractTo($extractTo);
            $zip->close();
        } else {
            $this->error('压缩包打开失败');
        }

        // 保存json到数据库

        $data = file_get_contents($extractTo.'/data.json');
        $data = json_decode($data, true);
        if (!$data) {
            $this->error('导入存储失败');
        }

        $list = $data['list'] ?? [];
        foreach ($list as $item) {
            $item['code'] = $item['code'].'_import'.uniqid();
            $item['title'] = $item['title'].'-import';
            unset($item['id']);
            unset($item['createtime']);
            unset($item['updatetime']);

            $this->model->create($item, true);
        }

        // copy图片到本地
        (function ($extractTo) {
            if (!config('upload.bucket') == 'local') {
                return false;
            }

            $source = $extractTo.'/images'; // 源文件夹路径
            $destination = ROOT_PATH.'/public'; // 目标文件夹路径

            if (!is_dir($source)) {
                return false;
            }

            // 创建目标文件夹
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            // 遍历源文件夹
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source,
                    RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                $target = $destination.'/'.$iterator->getSubPathName();

                if ($file->isDir()) {
                    // 创建目标子文件夹
                    if (!is_dir($target)) {
                        mkdir($target, 0755, true);
                    }
                } else {
                    // 复制文件
                    copy($file, $target);
                }
            }

            return true;
        })($extractTo);

        $this->success('成功导入'.count($list).'条数据');
    }
    //e***高级授权***//

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model

                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','code','title','desc','status','createtime','updatetime','author']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

}

<?php

namespace app\admin\controller\web;

use app\common\controller\Backend;
use app\common\library\Redis;
use app\common\library\SiteUtil;
use app\common\library\StandApi;
use app\common\model\WebModelDownload;
use think\exception\DbException;
use think\response\Json;

/**
 * 模板下载信息
 *
 * @icon fa fa-circle-o
 */
class SiteModel extends Backend
{

    /**
     * SiteModel模型对象
     * @var \app\admin\model\web\SiteModel
     */
    protected $model = null;
    protected $syncModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\web\SiteModel;
        $this->syncModel = new \app\common\model\SyncTemplates;
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        // 判断是否需要同步模板
        if (!Redis::getInstance()->exists(Redis::REDIS_KEY_TEMPLATE_SYNC_MARK)) {
            if (StandApi::getInstance()->syncTemplate()) {
                Redis::getInstance()->setex(Redis::REDIS_KEY_TEMPLATE_SYNC_MARK, Redis::REDIS_KEY_EXPIRE_ONE_DAY, time());
            }
        }
        $download_data_array = [];
        $download_array = $this->model->select();
        foreach ($download_array as $download) {
            $download_data_array[$download->model_id] = $download;
        }
        $template_data = [];
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $list = $this->syncModel
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        $templates = $list->items();
        foreach ($templates as $template) {
            $download = isset($download_data_array[$template['id']]) ? $download_data_array[$template['id']] : null;
            $template_data[] = [
                'id' => $template['id'],
                'model_id' => $template['id'],
                'model_cover' => $template['cover'],
                'model_name' => $template['name'],
                'model_version' => $template['version'],
                'update_time' => $template['updated_at'],
                'model_is_download' => (!empty($download)),
                'model_status' => $download ? $download['model_status_text'] : ''
            ];
        }
        $result = ['total' => $list->total(), 'rows' => $template_data];
        return json($result);
    }

    public function add()
    {
        $this->error(__('Invalid operation'));
    }

    public function edit($ids = null)
    {
        $this->error(__('Invalid operation'));
    }

    public function del($ids = null)
    {
        $this->error(__('Invalid operation'));
    }

    /**
     * 下载模板
     * @param null $ids
     */
    public function download($ids = null)
    {
        if (empty($ids)) {
            $this->error(__('No Results were found'));
        }
        $template = $this->syncModel->get($ids);
        if (empty($template['file'])) {
            $this->error(__('No Results were found'));
        }
        $template_name = SiteUtil::downloadSiteModel($template['file']);
        if (empty($template_name)) {
            $this->error(__('download failed'));
        }
        $_id = $this->model->insertGetId([
            'model_id' => $template['id'],
            'model_name' => $template['name'],
            'model_version' => $template['version'],
            'model_path' => $template_name,
            'model_theme' => $template['theme'],
            'model_status' => WebModelDownload::STATUS_ENABLE,
            'create_time' => time()
        ]);
        $_id ? $this->success() : $this->error(__('download failed'));
    }

    /**
     * 获取模板详情
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function detail($ids = null) {
        if (empty($ids)) {
            $this->error(__('No Results were found'));
        }
        $template = $this->syncModel->get($ids);
        if (empty($template['file'])) {
            $this->error(__('No Results were found'));
        }
        $this->assign('description', xss_clean($template['description']));
        return $this->view->fetch();
    }

}

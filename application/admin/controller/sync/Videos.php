<?php

namespace app\admin\controller\sync;

use app\common\controller\Backend;
use app\common\exception\VideosSyncEmptyException;
use app\common\library\handle\ConfigurationHandle;
use app\common\library\Redis;
use app\common\library\StandApi;
use app\common\library\VideoSync;
use think\Cache;
use think\exception\DbException;
use think\response\Json;

/**
 * 视频管理
 *
 * @icon fa fa-circle-o
 */
class Videos extends Backend
{

    /**
     * Videos模型对象
     * @var \app\admin\model\sync\Videos
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\sync\Videos;

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
            $this->assignconfig('isWin', IS_WIN);
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        // 是否在执行初始化操作
        if (Cache::has(ConfigurationHandle::getBasicCacheKeyInitVideosInfo())) {
            return json(['total' => 0, 'rows' => []]);
        }
        $max_id = VideoSync::getMaxVideosId();
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $page = StandApi::getInstance()->getVideos($max_id, $offset, $limit);
        foreach ($page['rows'] as &$row) {
            $row['time'] = format_duration($row['time'], true);
            $row['size'] = format_bytes($row['size']);
        }
        $result = ['total' => $page['total'], 'rows' => $page['rows']];
        return json($result);
    }

    /**
     * 选中同步（废弃，会改变现有视频的最大ID，导致有些视频无法再次同步）
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function selectsync()
    {
        if (Cache::has(ConfigurationHandle::getBasicCacheKeyInitVideosInfo())) {
            $this->error(__('Initialization of the video, please later'), '');
        }
        if ($this->request->isPost()) {
            $ids = $this->request->post('ids', '');
            $limit = $this->request->post('limit', 15);
            $offset = $this->request->post('offset', 0);
            if (empty($ids)) {
                $this->error(__('No data without synchronization'));
            }
            $ids = explode(",", $ids);
            if (empty($ids)) {
                $this->error(__('No data without synchronization'));
            }
            try {
                $sync_success_numer = VideoSync::selectSyncVideos($ids, $limit, $offset);
            } catch (VideosSyncEmptyException $ex) {
                $this->error(__('No data without synchronization'));
            }
            $this->success(__('Synchronous success, %s pieces of data this time', $sync_success_numer));
        }
        $this->error(__('No data without synchronization'));
    }

    /**
     * 全部同步
     * @return string
     * @throws DbException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function allsync()
    {
        if (Cache::has(ConfigurationHandle::getBasicCacheKeyInitVideosInfo())) {
            $this->error(__('Initialization of the video, please later'), '');
        }
        if (false === $this->request->isAjax()) {
            $this->view->assign('total', $this->request->get('total'));
            return $this->view->fetch();
        }
        // 更具系统环境不同进行不同的处理
        if (IS_WIN) {
            // Win环境下的同步处理机制
            $data = VideoSync::allSyncByWin($this->request->post('first', ''));
        } else {
            // 其它环境下的同步处理机制
            $data = VideoSync::allSync(Redis::REDIS_KEY_EXPIRE_FIVE_MINUTES);
        }
        $this->success('success', '', $data);
    }

}

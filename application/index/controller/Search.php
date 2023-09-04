<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\library\VideoUtil;
use app\common\model\SyncActresses;
use app\common\model\SyncVideos;

class Search extends Frontend
{

    protected $noNeedLogin = ['index'];

    /**
     * 搜索
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        $kw = $this->getUrlPathParams(0) ?: null;
        $pn = $this->getUrlPathParamsInt(1) ?: 1;
        $limit = $this->getUrlPathParamsInt(2) ?: 16;
        $order_field = $this->getUrlPathParams(3) ?: 'created_at';
        $order = $order_field ? [$order_field => 'DESC'] : null;
        // 获取条件
        $actresse = null;
        if ($kw) {
            // 匹配演员
            $actresses = SyncActresses::findSyncActressesByLikeName($kw, 1);
            $actresse = empty($actresses) ? null : $actresses[0];
        }
        // 搜索视频
        $page = SyncVideos::findSyncVideosBySearch($pn, $limit, $kw, $order);
        $page_html = $this->getPageHtml($pn, $limit, $page->total(), '/index/search/index/' . ($kw ? urlencode($kw) : 0));
        $this->view->assign('videoInfo', ['videoArray' => VideoUtil::getInstance()->processingVideoArray($page->items()), 'total' => $page->total(), 'pageHtml' => $page_html]);
        $this->view->assign('params', ['kw' => $kw, 'orderField' => $order_field]);
        // 展示第一个匹配的演员信息
        if ($actresse) {
            $this->view->assign('actresse', $actresse);
        }
        return $this->view->fetch();
    }

}
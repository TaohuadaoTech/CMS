<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\library\VideoUtil;
use app\common\model\SyncVideos;
use app\common\model\WebAdvertisement;

class Category extends Frontend
{

    protected $noNeedLogin = ['index'];

    /**
     * 全部分类
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
        // 获取横幅广告
        $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::STATIC_BANNER_ID, $this->mobileMark);
        if (!empty($array)) {
            $this->view->assign('staticBanner', $array);
        }
        // 获取视频
        $category_name = '全部';
        $sync_category_id = $this->getUrlPathParams(0) ?: 0;
        $order_field = $this->getUrlPathParams(1) ?: 'created_at';
        $pn = $this->getUrlPathParamsInt(2) ?: 1;
        $limit = $this->getUrlPathParamsInt(3) ?: 12;
        $category_data_map = VideoUtil::getInstance()->getCategoryDataMap();
        if ($sync_category_id) {
            $category = isset($category_data_map[$sync_category_id]) ? $category_data_map[$sync_category_id] : null;
            $category_name = empty($category) ? '未知' : $category['name'];
        }
        $order = [$order_field => 'DESC'];
        $page = SyncVideos::findSyncVideosByPage($pn, $limit, $sync_category_id, $order);
        $page_html = $this->getPageHtml($pn, $limit, $page->total(), "/index/category/index/{$sync_category_id}/{$order_field}");
        // 返回视频信息
        $this->view->assign('videoInfo', ['videoArray' => VideoUtil::getInstance()->processingVideoArray($page->items()), 'total' => $page->total(), 'pageHtml' => $page_html]);
        $this->view->assign('params', ['syncCategoryId' => $sync_category_id, 'categoryName' => $category_name, 'orderField' => $order_field]);
        return $this->view->fetch();
    }

}
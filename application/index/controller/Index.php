<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Redis;
use app\common\library\VideoUtil;
use app\common\model\SyncVideos;
use app\common\model\WebAdvertisement;
use app\common\model\WebCategorys;
use app\common\model\WebMessageRead;
use app\common\model\WebMessages;

class Index extends Frontend
{

    protected $noNeedLogin = ['*'];

    /**
     * 首页
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 获取 Banner 广告
        $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::DYNAMIC_BANNER_ID, $this->mobileMark);
        if (!empty($array)) {
            $this->view->assign('dynamicBanner', $array);
        }
        // 获取横幅广告
        $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::STATIC_BANNER_ID, $this->mobileMark);
        if (!empty($array)) {
            $this->view->assign('staticBanner', $array);
        }
        // 获取公告消息
        $web_message = WebMessages::findLastWebMessageInfo();
        if ($web_message) {
            $message_array = array($web_message);
            if ($this->auth->isLogin()) {
                WebMessageRead::checkWebMessageReadFlg($this->auth->getUserId(), $message_array);
            }
            if ($message_array[0]['read_flg'] == WebMessages::READ_FLG_NO) {
                $this->view->assign('messageList', $message_array);
            }
        }
        $videoUtil = VideoUtil::getInstance();
        // 获取排序靠前的几个分类
        $video_info_array = [];
        $order = ['views' => 'DESC'];
        $categorie_array = (new WebCategorys())->findWebCategorys(6, true);
        foreach ($categorie_array as $categorie) {
            $voideo_array = SyncVideos::findSyncVideosByPage(1, 8, $categorie['sync_id'], $order)->items();
            $video_info_array[] = [
                'syncCategorieId' => $categorie['sync_id'],
                'categorieName' => $categorie['name'],
                'categorieLogo' => $categorie['logo'],
                'categorieModel' => $categorie['mode'],
                'voideoArray' => $videoUtil->processingVideoArray($voideo_array)
            ];
        }
        $this->view->assign('videoInfoArray', $video_info_array);
        // 获取最近更新20条数据
        $this->view->assign('recentUpdateVideoArray', SyncVideos::findCacheVideoInfo(Redis::REDIS_KEY_RECENT_UPDATE_VIDEO_SET, 20, ['created_at' => 'DESC']));
        // 获取最近上市的20条数据
        $this->view->assign('recentLaunchedVideoArray', SyncVideos::findCacheVideoInfo(Redis::REDIS_KEY_RECENT_LAUNCHED_VIDEO_SET, 20, ['release_date' => 'DESC']));
        // 获取TOP10数据
        $this->view->assign('topTheVideoArray', SyncVideos::findCacheVideoInfo(Redis::REDIS_KEY_TOP_TEN_VIDEO_SET, 10, ['like' => 'DESC']));
        // 猜你喜欢20条数据
        $this->view->assign('youLikeVideoArray', SyncVideos::findCacheVideoInfo(Redis::REDIS_KEY_YOU_LIKE_VIDEO_SET, 20, ['favorites' => 'DESC']));
        // 返回模板视图
        return $this->view->fetch();
    }

}

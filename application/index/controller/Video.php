<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\exception\UserScoreNotEnoughException;
use app\common\library\handle\ConfigurationHandle;
use app\common\library\Redis;
use app\common\library\VideoUtil;
use app\common\model\SyncVideos;
use app\common\model\WebAdvertisement;
use app\common\model\WebCategorys;
use app\common\model\WebVideoCollection;
use app\common\model\WebVideoLike;
use app\common\model\WebVideoPurchase;
use app\common\model\WebVipPackages;

class Video extends Frontend
{

    protected $noNeedLogin = ['detail', 'newtop', 'checkuser', 'buyvideo'];

    /**
     * 视频详情页
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function detail()
    {
        $videoUtil = VideoUtil::getInstance();
        // 获取视频
        $vid = $this->getUrlPathParams(0);
        if (empty($vid) || empty($video = SyncVideos::findSyncVideosByVid($vid))) {
            $this->redirect('/');
        }
        $video = $videoUtil->processingVideo($video);
        $video['time_at'] = format_duration($video['time']);
        $video['tagsArray'] = explode(",", $video['tags']);
        $video['actressesArray'] = explode(",", $video['actresses']);
        // 用户是否登录
        if ($this->auth->isLogin()) {
            $user = $this->auth->getUser();
            // 是否点赞
            $video['upLikeMark'] = false;
            $video['downLikeMark'] = false;
            $like_array = WebVideoLike::findWebVideoLikeByUserId($user->id, $video['vid']);
            foreach ($like_array as $like) {
                if ($like->type == WebVideoLike::TYPE_SAVAGE) {
                    $video['upLikeMark'] = true;
                } else {
                    $video['downLikeMark'] = true;
                }
            }
            // 是否收藏
            $video_collection = WebVideoCollection::findWebVideoCollectionByUserId($user->id, $video['vid']);
            $video['collectionMark'] = !empty($video_collection);
            // 是否购买该影片
            if ($video['categorieModel'] == WebCategorys::MODE_REFLECT) {
                $user_not_bought = empty(WebVideoPurchase::findWebVideoPurchaseByUserId($user->id, $video['vid']));
            }
        }
        // 设置视频信息
        $video['userNotBought'] = isset($user_not_bought) ? $user_not_bought : true;
        $this->assignconfig('video', ['vid' => $vid, 'cover' => $video['cover'], 'm3u8Url' => $video['m3u8_url']]);
        $this->view->assign('video', $video);
        // 如果视频是VIP的话就获取三个VIP价格配置信息
        if ($video['categorieModel'] == WebCategorys::MODE_VIP) {
            $array = WebVipPackages::findWebVipPackages(3);
            $this->view->assign('vipPackages', $array);
        }
        // 获取片头广告
        $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::HEAD_ID, $this->mobileMark);
        if (!empty($array)) {
            $this->view->assign('headAdvertis', $array);
            $seconds = ConfigurationHandle::getSystemConfigSkipAdsAfterSeconds();
            $this->assignconfig('jumpOverAdvertis', $seconds && $seconds > 0 ? $seconds : 86400);
        }
        // 获取横幅广告
        $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::STATIC_BANNER_ID, $this->mobileMark);
        if (!empty($array)) {
            $this->view->assign('staticBanner', $array);
        }
        // 获取暂停广告
        $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::PAUSE_ID, $this->mobileMark);
        if (!empty($array)) {
            $this->view->assign('videoPause', $array);
        }
        // 获取推荐视频
        $video_array = SyncVideos::findSyncVideosByRecommend(16, explode(",", $video['actresses_id']), explode(",", $video['tags_id']), $video['category_id'], 'random', $video['id']);
        $this->view->assign('videoArray', $videoUtil->processingVideoArray($video_array));
        // 猜你喜欢20条视频
        $this->view->assign('youLikeVideoArray', SyncVideos::findCacheVideoInfo(Redis::REDIS_KEY_YOU_LIKE_VIDEO_SET, 20, ['favorites' => 'DESC']));
        // 返回视图
        return $this->view->fetch();
    }

    /**
     * 最新视频
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function newtop()
    {
        // 获取视频
        $pn = $this->getUrlPathParamsInt(0) ?: 1;
        $limit = $this->getUrlPathParamsInt(1) ?: 20;
        $order_field = $this->getUrlPathParams(2) ?: null;
        $order = $order_field ? [$order_field => 'DESC'] : null;
        $page = SyncVideos::findSyncVideosByPage($pn, $limit, null, $order);
        $total = $page->total();
        $video_array = $page->items();
        $page_html = $this->getPageHtml($pn, $limit, $total, '/index/video/newtop');
        $this->view->assign('videoInfo', ['videoArray' => VideoUtil::getInstance()->processingVideoArray($video_array), 'total' => $total, 'pageHtml' => $page_html]);
        $this->view->assign('params', ['orderField' => $order_field]);
        // 获取横幅广告
        $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::STATIC_BANNER_ID, $this->mobileMark);
        if (!empty($array)) {
            $this->view->assign('staticBanner', $array);
        }
        return $this->view->fetch();
    }

    /**
     * 检查用户是否可播放视频
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function checkuser()
    {
        $result_data = [];
        // 获取视频所属类型
        $vid = $this->request->post("vid");
        $video = SyncVideos::findSyncVideosByVid($vid);
        VideoUtil::getInstance()->processingVideo($video);
        if ($video['categorieModel'] == WebCategorys::MODE_FREE) {
            $result_data['login'] = true;
            $result_data['play'] = true;
        } else {
            if ($this->auth->isLogin()) {
                $user = $this->auth->getUser();
                $result_data['login'] = true;
                if ($video['categorieModel'] == WebCategorys::MODE_VIP) {
                    $result_data['play'] = $this->userVipMark;
                } else if ($video['categorieModel'] == WebCategorys::MODE_REFLECT) {
                    // 判断用户是否购买该视频
                    $result_data['play'] = !empty(WebVideoPurchase::findWebVideoPurchaseByUserId($user->id, $video->vid));
                } else {
                    $result_data['play'] = true;
                }
                $result_data['mode'] = $video['categorieModel'];
            } else {
                $result_data['login'] = false;
            }
        }
        $this->success('success', '', $result_data);
    }

    /**
     * 购买点映视频
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function buyvideo()
    {
        $result_data = [];
        if ($this->auth->isLogin()) {
            $user = $this->auth->getUser();
            $result_data['login'] = true;
            // 获取视频
            $vid = $this->request->post("vid");
            $video = SyncVideos::findSyncVideosByVid($vid);
            // 是否已购买
            if (WebVideoPurchase::findWebVideoPurchaseByUserId($user->id, $video->vid)) {
                $result_data['integralNotEnough'] = false;
            } else {
                $copy_video = clone $video;
                VideoUtil::getInstance()->processingVideo($video);
                // 判断积分是否足够
                $integral = $video['integralNumber'] ?: 0;
                if ($user->score >= $integral) {
                    try {
                        $result_data['integralNotEnough'] = false;
                        if (!WebVideoPurchase::addWebVideoPurchase($user->id, $copy_video, $integral)) {
                            $this->error(__('Failed purchase'));
                        }
                    } catch (UserScoreNotEnoughException $ex) {
                        $result_data['integralNotEnough'] = true;
                    }
                } else {
                    $result_data['integralNotEnough'] = true;
                }
            }
        } else {
            $result_data['login'] = false;
        }
        $this->success('success', '', $result_data);
    }

    /**
     * 点赞
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function like()
    {
        $vid = $this->request->param('vid');
        $type = $this->request->param('type');
        if (empty($vid) || empty($video = SyncVideos::findSyncVideosByVid($vid))) {
            $this->error(__('Video loss'));
        }
        if ($type == WebVideoLike::TYPE_STEPON) {
            $field = 'dislike';
        } else {
            $field = 'like';
        }
        $like = WebVideoLike::findWebVideoLikeByUserIdType($this->auth->getUserId(), $video->vid, $type);
        if (empty($like)) {
            $cancel = false;
            SyncVideos::plusOne($video->id, $field);
            WebVideoLike::addWebVideoLike($this->auth->getUserId(), $video, $type);
        } else {
            $cancel = true;
            SyncVideos::minusOne($video->id, $field);
            WebVideoLike::delWebVideoLikeById($like->id);
        }
        $this->success('success', '', ['cancel' => $cancel]);
    }

    /**
     * 收藏
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function collection()
    {
        $vid = $this->request->param('vid');
        if (empty($vid) || empty($video = SyncVideos::findSyncVideosByVid($vid))) {
            $this->error(__('Video loss'));
        }
        $collection = WebVideoCollection::findWebVideoCollectionByUserId($this->auth->getUserId(), $video->vid);
        if (empty($collection)) {
            $cancel = false;
            SyncVideos::plusOne($video->id, 'favorites');
            WebVideoCollection::addWebVideoCollection($this->auth->getUserId(), $video);
        } else {
            $cancel = true;
            SyncVideos::minusOne($video->id, 'favorites');
            WebVideoCollection::delWebVideoCollectionById($collection->id);
        }
        $this->success('success', '', ['cancel' => $cancel]);
    }

}
<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\library\VideoUtil;
use app\common\model\WebActivitySetting;
use app\common\model\WebChargeIntegral;
use app\common\model\WebMessages;
use app\common\model\WebPaymentMethod;
use app\common\model\WebUserSignLog;
use app\common\model\WebVideoCollection;
use app\common\model\WebVideoPurchase;
use app\common\model\User as UserModel;
use app\common\model\WebVipPackages;

class Personal extends Frontend
{

    protected $showTopAdvertising = false;
    protected $showBottomAdvertising = false;
    protected $showLeftAdvertising = false;
    protected $showRightAdvertising = false;

    /**
     * 个人中心
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
        $user = $this->auth->getUser();
        // 获取消息公告数量
        $this->view->assign('messageCount', WebMessages::findAllWebMessagesCount());
        // 获取我的收藏
        $video_collection_array = WebVideoCollection::findWebVideoCollectionByUserId($this->auth->getUserId(), null, 8);
        foreach ($video_collection_array as &$video) {
            $video['videoInfo'] = VideoUtil::getInstance()->processingVideo(json_decode($video['video_info'], true));
        }
        // 获取我的已购
        $video_purchase_array = WebVideoPurchase::findWebVideoPurchaseByUserId($this->auth->getUserId(), null, 8);
        foreach ($video_purchase_array as &$video) {
            $video['videoInfo'] = VideoUtil::getInstance()->processingVideo(json_decode($video['video_info'], true));
        }
        // 保存视频信息
        $this->view->assign('videoCollectionArray', $video_collection_array);
        $this->view->assign('videoPurchaseArray', $video_purchase_array);
        // 获取每天签到信息
        if (WebActivitySetting::getSignFlg()) {
            $day_sign_info = [];
            $image_type_active = ['1' => '3', '2' => '4'];
            $image_type_not_active = ['1' => '1', '2' => '2'];
            $continuity_give_type = WebActivitySetting::getContinuityGiveType();
            $continuity_give_number = WebActivitySetting::getContinuityGiveNumber();
            $chinese_number = [1 => '第一天', 2 => '第二天', 3 => '第三天', 4 => '第四天', 5 => '第五天', 6 => '第六天', 7 => '第七天'];
            $user_sign_log_array = (new WebUserSignLog())->findWebUserSignLogByUserLastSevenDays($user);
            for ($index = 1; $index <= 7; $index++) {
                $array = ['title' => $chinese_number[$index]];
                if (isset($user_sign_log_array[$index - 1])) {
                    $array['active'] = '';
                    $array['imageType'] = $image_type_not_active[$continuity_give_type];
                } else {
                    $array['active'] = 'active';
                    $array['imageType'] = $image_type_active[$continuity_give_type];
                }
                $array['text'] = $continuity_give_number . $this->getActivityTypeStr($continuity_give_type);
                array_push($day_sign_info, $array);
            }
            $today_sign_mark = in_array(date('Ymd'), $user_sign_log_array);
            $yesterda_sign_mark = in_array(date('Ymd', strtotime('-1 day')), $user_sign_log_array);
            if ($yesterda_sign_mark) {
                $successive_sign_days = $user->successive_sign_days;
            } else if ($today_sign_mark) {
                $successive_sign_days = 1;
            } else {
                $successive_sign_days = 0;
            }
            $this->view->assign('daySignInfo', $day_sign_info);
            $this->view->assign('todaySignMark', $today_sign_mark);
            $this->view->assign('successiveSignDays', $successive_sign_days);
        }
        // 获取累计邀请人数
        $this->view->assign('fromInviteNumber', UserModel::findUserCountByInviteCode($user->my_invite_code, 'from_invite_code'));
        // 获取邀请注册活动规则
        if (WebActivitySetting::getInviteFlg()) {
            $this->view->assign('inviteSetting', [
                'inviteStageNumber' => WebActivitySetting::getInviteStageNumber(),
                'inviteStageGiveType' => $this->getActivityTypeStr(WebActivitySetting::getInviteStageGiveType()),
                'inviteStageGiveNumber' => WebActivitySetting::getInviteStageGiveNumber(),
                'inviteTotalNumber' => WebActivitySetting::getInviteTotalNumber(),
                'inviteTotalGiveType' => $this->getActivityTypeStr(WebActivitySetting::getInviteTotalGiveType()),
                'inviteTotalGiveNumber' => WebActivitySetting::getInviteTotalGiveNumber()
            ]);
        }
        // 获取VIP价格配置信息
        $this->view->assign('vipPackages', WebVipPackages::findWebVipPackages());
        // 获取积分价格配置信息
        $this->view->assign('chargeIntegralArray', WebChargeIntegral::findWebChargeIntegral());
        // 获取所支持的支付方式
        $this->assignconfig('channelManually', WebPaymentMethod::CHANNEL_MANUALLY);
        $this->view->assign('paymentMethodArray', WebPaymentMethod::findWebPaymentMethod());
        // 返回视图
        return $this->view->fetch();
    }

    /**
     * 用户签到
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function usersign()
    {
        $user = UserModel::find($this->auth->getUserId());
        // 添加签到记录
        WebUserSignLog::addWebUserSignLog($user->id);
        // 验证昨天是否签到
        $today_sign = WebUserSignLog::findWebUserSignLogByYesterday($user->id);
        if (empty($today_sign)) {
            $successive_sign_days = 1;
            $max_successive_sign_days = max($user->successive_sign_days, $user->max_successive_sign_days);
        } else {
            $successive_sign_days = $user->successive_sign_days + 1;
            $max_successive_sign_days = $user->max_successive_sign_days + 1;
        }
        // 更新用户基本信息
        UserModel::updateUserInfoByUserId($user->id, [
            'updatetime' => time(),
            'successive_sign_days' => $successive_sign_days,
            'max_successive_sign_days' => $max_successive_sign_days
        ]);
        // 派送相应的签到奖励
        if (WebActivitySetting::getSignFlg()) {
            // 每天签到赠送
            $continuity_give_number = WebActivitySetting::getContinuityGiveNumber();
            switch (WebActivitySetting::getContinuityGiveType()) {
                case WebActivitySetting::TYPE_VIP:
                    UserModel::viptime($continuity_give_number * 86400, $user->id, '用户每日签到赠送');
                    break;
                case WebActivitySetting::TYPE_INTEGRAL:
                    UserModel::score(intval($continuity_give_number), $user->id, '用户每日签到赠送');
                    break;
            }
            // 累计签到赠送
            if ($successive_sign_days % 7 == 0) {
                $seven_days_give_number = WebActivitySetting::getSevenDaysGiveNumber();
                switch (WebActivitySetting::getSevenDaysGiveType()) {
                    case WebActivitySetting::TYPE_VIP:
                        UserModel::viptime($seven_days_give_number * 86400, $user->id, '用户连续七天签到赠送');
                        break;
                    case WebActivitySetting::TYPE_INTEGRAL:
                        UserModel::score(intval($seven_days_give_number), $user->id, '用户连续七天签到赠送');
                        break;
                }
            }
        }
        $this->success();
    }

    /**
     * 我的收藏
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function collection()
    {
        $user_id = $this->auth->getUserId();
        $videUtil = VideoUtil::getInstance();
        $pn = $this->getUrlPathParamsInt(0) ?: 1;
        $limit = $this->getUrlPathParamsInt(1) ?: 15;
        $page = WebVideoCollection::findWebVideoCollectionByPage($user_id, $pn, $limit);
        $page_html = $this->getPageHtml($pn, $limit, $page->total(), '/index/personal/collection');
        $video_array = $page->items();
        foreach ($video_array as &$video) {
            $video['videoInfo'] = $videUtil->processingVideo(json_decode($video['video_info'], true));
        }
        $this->view->assign('videoInfo', ['videoArray' => $video_array, 'pageHtml' => $page_html]);
        return $this->view->fetch();
    }

    /**
     * 已购影片
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function purchase()
    {
        $user_id = $this->auth->getUserId();
        $videUtil = VideoUtil::getInstance();
        $pn = $this->getUrlPathParamsInt(0) ?: 1;
        $limit = $this->getUrlPathParamsInt(1) ?: 15;
        $page = WebVideoPurchase::findWebVideoPurchasePage($user_id, $pn, $limit);
        $page_html = $this->getPageHtml($pn, $limit, $page->total(), '/index/personal/purchase');
        $video_array = $page->items();
        foreach ($video_array as &$video) {
            $video['videoInfo'] = $videUtil->processingVideo(json_decode($video['video_info'], true));
        }
        $this->view->assign('videoInfo', ['videoArray' => $video_array, 'pageHtml' => $page_html]);
        return $this->view->fetch();
    }

    /**
     * 获取活动类型名称
     * @param $invite_type
     * @return mixed
     */
    private function getActivityTypeStr($invite_type)
    {
        if (WebActivitySetting::TYPE_VIP == $invite_type) {
            return __('Sky VIP');
        } else {
            return __('Integral');
        }
    }

}
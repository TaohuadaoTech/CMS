<?php


namespace app\common\library;


use app\common\exception\VideosSyncEmptyException;
use app\common\model\SyncVideos;

class VideoSync
{

    /**
     * 同步选择的视频
     * @param $ids
     * @param $limit
     * @param $offset
     * @return int
     * @throws VideosSyncEmptyException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function selectSyncVideos($ids, $limit, $offset)
    {
        // 获取需要同步的视频信息
        $page = StandApi::getInstance()->getVideos(self::getMaxVideosId(), $offset, $limit, 1);
        if (empty($page['rows'])) {
            throw new VideosSyncEmptyException();
        }
        // 同步视频前先同步视频源和分类信息
        StandApi::getInstance()->syncOrigin();
        StandApi::getInstance()->syncCategory();
        // 同步选中的视频
        $sync_success_numer = 0;
        foreach ($page['rows'] as $video) {
            if (in_array($video['id'], $ids)) {
                if (StandApi::getInstance()->syncVideos($video)) {
                    $sync_success_numer++;
                }
            }
        }
        return $sync_success_numer;
    }

    /**
     * 全部同步（Win版本）
     * @param $first
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function allSyncByWin($first)
    {
        // 自动同步服务是否在进行中
        if (Redis::getInstance()->exists(Redis::REDIS_KEY_VIDEO_SYNC_MARK)) {
            return self::getSyncQueueData();
        } else {
            if (!empty($first)) {
                // 同步视频前先同步视频源和分类信息
                StandApi::getInstance()->syncOrigin();
                StandApi::getInstance()->syncCategory();
            }
            // 获取本次同步数据
            $limit = 100;
            $video_name_array = [];
            $page = StandApi::getInstance()->getVideos(self::getMaxVideosId(), 0, $limit, 1);
            if (empty($page['rows'])) {
                $all_sync_success = true;
            } else {
                foreach ($page['rows'] as $row) {
                    StandApi::getInstance()->syncVideos($row);
                    $video_name_array[] = $row['name'];
                }
                $all_sync_success = false;
            }
            return self::getResultData($video_name_array, $all_sync_success);
        }
    }

    /**
     * 全部同步
     * @param $expire
     * @return array|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function allSync($expire)
    {
        $redis_key_queue = Redis::REDIS_KEY_VIDEO_SYNC_SUCCESS_INFO;
        $redis_key_success = Redis::REDIS_KEY_VIDEO_SYNC_SUCCESS_MARK;
        if (Redis::getInstance()->set(Redis::REDIS_KEY_VIDEO_SYNC_MARK, time(), ['nx', 'ex' => Redis::REDIS_KEY_EXPIRE_THREE_HOURS])) {
            Redis::getInstance()->del($redis_key_queue, $redis_key_success);
            if (!IS_CLI) {
                // 自动断开HTTP请求并保证脚本继续执行
                fastcgi_finish_request();
                session_write_close();
            }
            // 同步视频前先同步视频源和分类信息
            StandApi::getInstance()->syncOrigin();
            StandApi::getInstance()->syncCategory();
            // 分页请求同步数据
            $total = 0;
            $limit = StandApi::getInstance()->getLimit();
            do {
                // 获取需要同步的视频信息
                $page = StandApi::getInstance()->getVideos(self::getMaxVideosId(), 0, $limit, 1);
                $total = $total === 0 ? $page['total'] : $total;
                if (empty($page['rows'])) {
                    break;
                }
                foreach ($page['rows'] as $row) {
                    StandApi::getInstance()->syncVideos($row);
                    Redis::getInstance()->rPush($redis_key_queue, $row['name']);
                }
            } while ($page['total'] > $limit);
            // 同步完成后
            Redis::getInstance()->set($redis_key_success, time());
            Redis::getInstance()->expire(Redis::REDIS_KEY_VIDEO_SYNC_MARK, $expire);
            // 返回总同步数
            return $total;
        } else {
            return self::getSyncQueueData();
        }
    }

    /**
     * 获取已同步的最大视频ID
     * @return int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMaxVideosId()
    {
        $max_id = 0;
        $last_video = SyncVideos::order('id', 'DESC')->find();
        if (!empty($last_video)) {
            $max_id = $last_video['id'];
        }
        return $max_id;
    }

    /**
     * 获取同步队列数据
     * @return array
     */
    private static function getSyncQueueData()
    {
        $all_sync_success = false;
        // 获取同步成功的视频名称信息
        $video_name_array = [];
        $redis_key_queue = Redis::REDIS_KEY_VIDEO_SYNC_SUCCESS_INFO;
        $success_number = Redis::getInstance()->lLen($redis_key_queue);
        $success_number = $success_number > 500 ? 500 : $success_number;
        for ($index = 0; $index < $success_number; $index++) {
            $video_name_array[] = Redis::getInstance()->lPop($redis_key_queue);
        }
        if (empty($video_name_array)) {
            if (Redis::getInstance()->exists(Redis::REDIS_KEY_VIDEO_SYNC_SUCCESS_MARK)) {
                $all_sync_success = true;
            }
        }
        return self::getResultData($video_name_array, $all_sync_success);
    }

    /**
     * 包装返回数据
     * @param $video_name_array
     * @param $all_sync_success
     * @return array
     */
    private static function getResultData($video_name_array, $all_sync_success)
    {
        if ($all_sync_success && empty($video_name_array)) {
            $video_name_array[] = '片源已全部同步完成';
        }
        return [
            'names' => $video_name_array,
            'all_sync_success' => $all_sync_success
        ];
    }

}
<?php


namespace app\common\model;


use app\common\exception\UserScoreNotEnoughException;
use app\common\library\Logger;
use think\Db;
use think\Model;

class WebVideoPurchase extends Model
{

    // 当前模型名称
    protected $name = 'web_video_purchase';

    /**
     * 购买点映视频
     * @param $user_id
     * @param $video
     * @param $score
     * @return bool
     * @throws UserScoreNotEnoughException
     */
    public static function addWebVideoPurchase($user_id, $video, $score)
    {
        try {
            Db::startTrans();
            $_id = self::insertGetId([
                'user_id' => $user_id,
                'score' => $score,
                'video_vid' => $video->vid,
                'create_time' => time(),
                'video_info' => json_encode($video, JSON_UNESCAPED_UNICODE)
            ]);
            if ($_id) {
                User::score(-$score, $user_id, "购买点映视频");
            }
            Db::commit();
            return true;
        } catch (UserScoreNotEnoughException $e) {
            Db::rollback();
            throw $e;
        } catch (\Exception $ex) {
            Db::rollback();
            Logger::error("WebVideoPurchase::addWebVideoPurchase => 积分购买影片异常: " . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
        return false;
    }

    /**
     * 获取已购信息
     * @param $user_id
     * @param null $video_vid
     * @param null $limit
     * @return array|false|\PDOStatement|string|\think\Collection|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebVideoPurchaseByUserId($user_id, $video_vid = null, $limit = null)
    {
        $where = self::where('user_id', $user_id);
        if ($video_vid) {
            return $where->where('video_vid', $video_vid)->find();
        }
        if ($limit) {
            $where->limit($limit);
        }
        return $where->order('create_time', 'DESC')->select();
    }

    /**
     * 分页获取已购信息
     * @param $user_id
     * @param $pn
     * @param $limit
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function findWebVideoPurchasePage($user_id, $pn, $limit)
    {
        return self::where('user_id', $user_id)->order('create_time', 'DESC')->paginate(['page' => $pn, 'list_rows' => $limit]);
    }

}
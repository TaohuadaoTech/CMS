<?php


namespace app\common\model;


use think\Model;

class WebVideoLike extends Model
{

    const TYPE_SAVAGE = 0;
    const TYPE_STEPON = 1;

    // 当前模型名称
    protected $name = 'web_video_like';

    /**
     * 新增点赞记录
     * @param $user_id
     * @param $video
     * @param $type
     * @return int|string
     */
    public static function addWebVideoLike($user_id, $video, $type)
    {
        return self::insertGetId([
            'user_id' => $user_id,
            'video_vid' => $video->vid,
            'type' => $type,
            'create_time' => time(),
            'video_info' => json_encode($video, JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * 获取用户点赞记录
     * @param $user_id
     * @param $video_vid
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebVideoLikeByUserId($user_id, $video_vid, $limit = 2)
    {
        return self::where('user_id', $user_id)->where('video_vid', $video_vid)->limit($limit)->select();
    }

    /**
     * 获取用户指定类型的点赞记录
     * @param $user_id
     * @param $video_vid
     * @param $type
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebVideoLikeByUserIdType($user_id, $video_vid, $type)
    {
        return self::where('user_id', $user_id)->where('video_vid', $video_vid)->where('type', $type)->find();
    }

    /**
     * 删除点赞记录
     * @param $like_id
     * @return int
     */
    public static function delWebVideoLikeById($like_id)
    {
        return self::where('id', $like_id)->delete();
    }

}
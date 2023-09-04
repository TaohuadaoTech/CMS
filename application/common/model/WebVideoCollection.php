<?php


namespace app\common\model;


use think\Model;

class WebVideoCollection extends Model
{

    // 当前模型名称
    protected $name = 'web_video_collection';

    /**
     * 添加收藏记录
     * @param $user_id
     * @param $video
     * @return int|string
     */
    public static function addWebVideoCollection($user_id, $video)
    {
        return self::insertGetId([
            'user_id' => $user_id,
            'video_vid' => $video->vid,
            'create_time' => time(),
            'video_info' => json_encode($video, JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * 获取用户收藏记录信息
     * @param $user_id
     * @param null $video_vid
     * @param int $limit
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebVideoCollectionByUserId($user_id, $video_vid = null, $limit = 0)
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
     * 分页获取用户收藏记录信息
     * @param $user_id
     * @param $pn
     * @param $limit
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function findWebVideoCollectionByPage($user_id, $pn, $limit)
    {
        return self::where('user_id', $user_id)->order('create_time', 'DESC')->paginate(['page' => $pn, 'list_rows' => $limit]);
    }

    /**
     * 删除收藏记录
     * @param $collection_id
     * @return int
     */
    public static function delWebVideoCollectionById($collection_id)
    {
        return self::where('id', $collection_id)->delete();
    }

}
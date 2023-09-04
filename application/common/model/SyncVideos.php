<?php


namespace app\common\model;


use app\common\library\Redis;
use app\common\library\VideoUtil;
use think\Model;

class SyncVideos extends Model
{

    // 当前模型名称
    protected $name = 'sync_videos';

    /**
     * 同步数据
     * @param $video
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sync($video)
    {
        if (empty($this->field)) {
            $this->field = $this->getQuery()->getTableInfo('', 'fields');
        }
        $database_data = $this->where('vid', $video['vid'])->find();
        if (empty($database_data)) {
            $this->isUpdate(false);
            $this->save($video);
        } else {
            $allowFields = $this->checkAllowField();
            foreach ($video as $key => $value) {
                if (!in_array($key, $allowFields)) {
                    unset($video[$key]);
                }
            }
            unset($video['id'], $video['views'], $video['favorites'], $video['like'], $video['dislike']);
            $this->where('id', $database_data->id)->setField($video);
        }
    }

    /**
     * 加一
     * @param $video_id
     * @param $field
     * @return int|true
     * @throws \think\Exception
     */
    public static function plusOne($video_id, $field)
    {
        return self::where('id', $video_id)->setInc($field);
    }

    /**
     * 减一
     * @param $video_id
     * @param $field
     * @return int|true
     * @throws \think\Exception
     */
    public static function minusOne($video_id, $field)
    {
        return self::where('id', $video_id)->setDec($field);
    }

    /**
     * 获取视频
     * @param $vid
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findSyncVideosByVid($vid)
    {
        return self::where('vid', $vid)->find();
    }

    /**
     * 分页获取视频
     * @param $pn
     * @param $list_rows
     * @param null $category_id
     * @param null $order
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function findSyncVideosByPage($pn, $list_rows, $category_id = null, $order = null)
    {
        $where = self::order(empty($order) ? ['created_at' => 'DESC'] : $order);
        if ($category_id) {
            $where->where('category_id', $category_id);
        }
        return $where->paginate(['page' => $pn, 'list_rows' => $list_rows]);
    }

    /**
     * 搜索视频
     * @param $pn
     * @param $list_rows
     * @param $kw
     * @param null $order
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function findSyncVideosBySearch($pn, $list_rows, $kw, $order = null)
    {
        $where = self::order(empty($order) ? ['created_at' => 'DESC'] : $order);
        if ($kw) {
            $where->whereOr('name', 'LIKE', "%{$kw}%")->whereOr('tags', 'LIKE', "%{$kw}%")->whereOr('actresses', 'LIKE', "%{$kw}%");
        }
        return $where->paginate(['page' => $pn, 'list_rows' => $list_rows]);
    }

    /**
     * 获取推荐视频
     * @param $limit
     * @param null $actresses_id_array
     * @param null $tag_id_array
     * @param null $category_id
     * @param null $order
     * @param null $not_eq_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findSyncVideosByRecommend($limit, $actresses_id_array = null, $tag_id_array = null, $category_id = null, $order = null, $not_eq_id = null)
    {
        if ($order === 'random') {
            $order_types = ['ASC', 'DESC'];
            $order_fields = ['time', 'views', 'favorites', 'like', 'dislike', 'release_date', 'created_at'];
            $order = [rand(0, count($order_fields) - 1) => rand(0, count($order_types) - 1)];
        }
        $where = self::order(empty($order) ? ['views' => 'DESC', 'like' => 'DESC'] : $order);
        if (!empty($actresses_id_array)) {
            $actresses_id_where_sql = '';
            foreach ($actresses_id_array as $actresses_id) {
                if (empty($actresses_id)) {
                    continue;
                }
                if ($actresses_id_where_sql) {
                    $actresses_id_where_sql .= 'OR ';
                }
                $actresses_id_where_sql .= "FIND_IN_SET('{$actresses_id}', `actresses_id`) ";
            }
            if ($actresses_id_where_sql) {
                $where->whereOrRaw($actresses_id_where_sql);
            }
        }
        if (!empty($tag_id_array)) {
            $tag_id_where_sql = '';
            foreach ($tag_id_array as $tag_id) {
                if (empty($tag_id)) {
                    continue;
                }
                if ($tag_id_where_sql) {
                    $tag_id_where_sql .= 'OR ';
                }
                $tag_id_where_sql .= "FIND_IN_SET('{$tag_id}', `tags_id`) ";
            }
            if ($tag_id_where_sql) {
                $where->whereOrRaw($tag_id_where_sql);
            }
        }
        if ($category_id) {
            $where->whereOr('category_id', $category_id);
        }
        if ($not_eq_id) {
            $where->where('id', '<>', $not_eq_id);
        }
        return $where->limit($limit)->select();
    }

    /**
     * 获取相应视频信息
     * @param $redis_key
     * @param $limit
     * @param $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findCacheVideoInfo($redis_key, $limit, $order)
    {
        $redis_video_array = Redis::getInstance()->sMembers($redis_key);
        if (empty($video_array)) {
            $video_array = self::findSyncVideosByRecommend($limit, null, null, null, $order);
            foreach ($video_array as $key => $value) {
                $redis_video_array[$key] = json_encode(VideoUtil::getInstance()->processingVideo($value), JSON_UNESCAPED_UNICODE);
            }
            Redis::getInstance()->sAddArray($redis_key, $redis_video_array);
            Redis::getInstance()->expire($redis_key, Redis::REDIS_KEY_EXPIRE_ONE_DAY);
        }
        foreach ($redis_video_array as $key => $value) {
            $redis_video_array[$key] = json_decode($value, true);
        }
        return $redis_video_array;
    }

}
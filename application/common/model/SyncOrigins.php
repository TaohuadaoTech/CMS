<?php


namespace app\common\model;


use app\common\library\Redis;
use think\Model;

class SyncOrigins extends Model
{

    // 当前模型名称
    protected $name = 'sync_origins';

    /**
     * 同步数据
     * @param $origins
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sync($origins)
    {
        if (empty($this->field)) {
            $this->field = $this->getQuery()->getTableInfo('', 'fields');
        }
        $origins_id = $origins['id'];
        $database_data = $this->find($origins_id);
        if (empty($database_data)) {
            $this->isUpdate(false);
            $this->save($origins);
        } else {
            $allowFields = $this->checkAllowField();
            foreach ($origins as $key => $value) {
                if (!in_array($key, $allowFields)) {
                    unset($origins[$key]);
                }
            }
            unset($origins['id']);
            $this->where('id', $database_data->id)->setField($origins);
        }
        self::findSyncOriginsByOriginIdCache($origins_id, false);
    }

    /**
     * 获取视频源信息（使用缓存）
     * @param null $origin_id
     * @param bool $read_cache
     * @return array|false|mixed|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findSyncOriginsByOriginIdCache($origin_id = null, $read_cache = true)
    {
        $redis_key = Redis::REDIS_KEY_WEB_ORIGINS_MAP;
        if ($origin_id) {
            $redis_value = $read_cache ? Redis::getInstance()->hGet($redis_key, $origin_id) : '';
            if (empty($redis_data)) {
                $origins = self::find($origin_id);
                $redis_value = json_encode($origins, JSON_UNESCAPED_UNICODE);
                Redis::getInstance()->hSet($redis_key, $origin_id, $redis_value);
            }
            return json_decode($redis_value, true);
        } else {
            $redis_value_array = $read_cache ? Redis::getInstance()->hGetAll($redis_key) : [];
            if (empty($redis_value_array)) {
                $origins_array = self::select();
                foreach ($origins_array as $origins) {
                    Redis::getInstance()->hSet($redis_key, $origins['id'], json_encode($origins, JSON_UNESCAPED_UNICODE));
                }
                return $origins_array;
            }
            $result_array = [];
            foreach ($redis_value_array as $value) {
                $result_array[] = json_decode($value, true);
            }
            return $result_array;
        }
    }

}
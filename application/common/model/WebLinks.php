<?php


namespace app\common\model;


use app\common\library\Redis;
use think\Model;

class WebLinks extends Model
{

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    // 当前模型名称
    protected $name = 'web_links';

    /**
     * 获取友链信息
     * @param bool $read_cache
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findAllWebLinks($read_cache = true)
    {
        $redis_value = $read_cache ? Redis::getInstance()->get(Redis::REDIS_KEY_WEB_LINKS_INFO) : '';
        if (empty($redis_value)) {
            $links = self::where('status', self::STATUS_ENABLE)->order('index')->select();
            $redis_value = json_encode($links, JSON_UNESCAPED_UNICODE);
            Redis::getInstance()->set(Redis::REDIS_KEY_WEB_LINKS_INFO, $redis_value);
        }
        return json_decode($redis_value, true);
    }

}
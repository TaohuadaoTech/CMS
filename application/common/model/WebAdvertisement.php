<?php


namespace app\common\model;


use app\common\library\Redis;
use think\Model;

class WebAdvertisement extends Model
{

    const TYPE_FIXED = 1;
    const TYPE_JS = 2;
    const TYPE_MULT = 3;
    const TYPE_VIDEO = 4;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    const STATIC_BANNER_ID = 1;
    const DYNAMIC_BANNER_ID = 2;
    const RIGHT_ID = 3;
    const LEFT_ID = 4;
    const ALLIANCE_JS_ID = 5;
    const START_IMAGE_ID = 6;
    const START_WINDOW_ID = 7;
    const PAUSE_ID = 8;
    const HEAD_ID = 9;
    const BOTTOM_ID = 10;
    const TOP_ID = 11;

    // 当前模型名称
    protected $name = 'web_advertisement';

    /**
     * 通过ID获取广告
     * @param $id
     * @param $mobile_mark
     * @param bool $read_cache
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebAdvertisementById($id, $mobile_mark, $read_cache = true)
    {
        $redis_key = Redis::REDIS_KEY_WEB_ADVERTISEMENT_INFO_HASHMAP;
        $redis_value = $read_cache ? Redis::getInstance()->hGet($redis_key, $id) : '';
        if (empty($redis_value)) {
            $web = self::where('id', $id)->where('status', self::STATUS_ENABLE)->find();
            $data = empty($web) ? [] : $web->toArray();
            if (!empty($data)) {
                if ($data['type'] == self::TYPE_FIXED || $data['type'] == self::TYPE_VIDEO) {
                    $result_data = ['url' => $data['url'], 'title' => $data['title'], 'image_pc' => $data['image_pc'], 'image_h5' => $data['image_h5'], 'image_url' => ''];
                } else if ($data['type'] == self::TYPE_JS) {
                    $result_data = $data['url'];
                } else if ($data['type'] == self::TYPE_MULT) {
                    $url_array = json_decode($data['url'], true);
                    $title_array = json_decode($data['title'], true);
                    $image_h5_array = json_decode($data['image_h5'], true);
                    $image_pc_array = json_decode($data['image_pc'], true);
                    $min_size = min(count($url_array), count($title_array), count($image_h5_array), count($image_pc_array));
                    for ($index = 0; $index < $min_size; $index++) {
                        $result_data[] = [
                            'url' => $url_array[$index],
                            'title' => $title_array[$index],
                            'image_pc' => $image_pc_array[$index],
                            'image_h5' => $image_h5_array[$index],
                            'image_url' => ''
                        ];
                    }
                }
            }
            $result_data = isset($result_data) ? $result_data : [];
            Redis::getInstance()->hSet($redis_key, $id, json_encode($result_data, JSON_UNESCAPED_UNICODE));
        } else {
            $result_data = json_decode($redis_value, true);
        }
        if (isset($result_data['image_url'])) {
            if ($mobile_mark === false) {
                $result_data['image_url'] = $result_data['image_pc'];
            } else if ($mobile_mark === true) {
                $result_data['image_url'] = $result_data['image_h5'];
            }
        } else if (is_array($result_data)) {
            foreach ($result_data as &$data) {
                if (isset($data['image_url'])) {
                    if ($mobile_mark === false) {
                        $data['image_url'] = $data['image_pc'];
                    } else if ($mobile_mark === true) {
                        $data['image_url'] = $data['image_h5'];
                    }
                }
            }
        }
        return $result_data;
    }

}
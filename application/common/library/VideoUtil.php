<?php


namespace app\common\library;


use app\common\model\SyncOrigins;
use app\common\model\WebCategorys;

class VideoUtil
{

    private static $_instance;

    /**
     * 初始化
     * VideoUtil constructor.
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    private function __construct()
    {
        $origin_array = SyncOrigins::findSyncOriginsByOriginIdCache(null);
        foreach ($origin_array as $origin) {
            $this->origin_data_map[$origin['id']] = $origin;
        }
        $web_categorys = (new WebCategorys())->findWebCategorysByCache();
        foreach ($web_categorys as $category) {
            $this->category_data_map[$category['sync_id']] = $category;
        }
    }

    private $origin_data_map;
    private $category_data_map;

    /**
     * 处理视频
     * @param $video
     * @return mixed
     */
    public function processingVideo($video)
    {
        if (isset($this->origin_data_map[$video['origin_id']])) {
            $origin = $this->origin_data_map[$video['origin_id']];
            $video['cover'] = "{$origin['img_url']}{$video['cover']}";
            $video['m3u8_url'] = "/index/m3u8/video/{$video['vid']}.m3u8";
            $video['share_url'] = "{$origin['video_url']}{$video['share_url']}";
        } else {
            Logger::write("VideoUtil::processingVideo => 无此视频源信息: " . $video['origin_id']);
        }
        if (isset($this->category_data_map[$video['category_id']])) {
            $category = $this->category_data_map[$video['category_id']];
            $video['categorieModel'] = $category['mode'];
            $video['categorieName'] = $category['name'];
            $video['integralNumber'] = $category['integral'];
        } else {
            $video['integralNumber'] = 0;
            $video['categorieName'] = '未知';
            $video['categorieModel'] = WebCategorys::MODE_FREE;
            Logger::write("VideoUtil::processingVideo => 无此视频分类信息: " . $video['category_id']);
        }
        return $video;
    }

    /**
     * 处理视频
     * @param $video_array
     * @return mixed
     */
    public function processingVideoArray($video_array)
    {
        foreach ($video_array as &$video) {
            $this->processingVideo($video);
        }
        return $video_array;
    }

    /**
     * 获取视频源数据
     * @return mixed
     */
    public function getOriginDataMap()
    {
        return $this->origin_data_map;
    }

    /**
     * 获取分类数据
     * @return mixed
     */
    public function getCategoryDataMap()
    {
        return $this->category_data_map;
    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

}
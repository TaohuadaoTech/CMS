<?php


namespace app\common\model;


use think\Model;

class WebModelDownload extends Model
{

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    // 当前模型名称
    protected $name = 'web_model_download';

    /**
     * 获取下载的模板信息
     * @param null $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebModelDownloadList($field = null)
    {
        return self::where('model_status', self::STATUS_ENABLE)->field($field)->select();
    }

}
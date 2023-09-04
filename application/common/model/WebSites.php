<?php


namespace app\common\model;


use think\Model;

class WebSites extends Model
{

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    // 当前模型名称
    protected $name = 'web_sites';

    /**
     * 获取所有站点信息
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebSites()
    {
         return self::where('status', self::STATUS_ENABLE)->select();
    }

    /**
     * 获取站点信息
     * @param $domain
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebSitesByDomain($domain)
    {
        return self::where('domain', $domain)->where('status', self::STATUS_ENABLE)->find();
    }

}
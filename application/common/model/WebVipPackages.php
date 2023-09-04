<?php


namespace app\common\model;


use think\Model;

class WebVipPackages extends Model
{

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    // 当前模型名称
    protected $name = 'web_vip_packages';

    /**
     * 获取VIP价格配置信息
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebVipPackages($limit = 0)
    {
        $where = self::where('status', self::STATUS_ENABLE);
        if ($limit) {
            $where->limit($limit)->order('id', 'DESC');
        }
        return $where->select();
    }

}
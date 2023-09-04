<?php


namespace app\common\model;


use think\Model;

class WebChargeIntegral extends Model
{

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    // 当前模型名称
    protected $name = 'web_charge_integral';

    /**
     * 获取积分价值配置信息
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebChargeIntegral()
    {
        return self::where('status', self::STATUS_ENABLE)->order('index')->select();
    }

}
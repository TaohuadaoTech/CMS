<?php


namespace app\common\model;


use app\common\library\Redis;
use think\Model;

class WebActivitySetting extends Model
{

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    const TYPE_VIP = 1;
    const TYPE_INTEGRAL = 2;

    // 当前模型名称
    protected $name = 'web_activity_setting';

    /**
     * 签到活动开启标志
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSignFlg()
    {
        return self::getWebActivitySetting('sign_flg');
    }

    /**
     * 每天签到赠送类型
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getContinuityGiveType()
    {
        return self::getWebActivitySetting('continuity_give_type');
    }

    /**
     * 每天签到赠送VIP天数或者积分数量
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getContinuityGiveNumber()
    {
        return self::getWebActivitySetting('continuity_give_number');
    }

    /**
     * 7天连续签到赠送类型
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSevenDaysGiveType()
    {
        return self::getWebActivitySetting('seven_days_give_type');
    }

    /**
     * 7天连续签到赠送VIP天数或者积分数量
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSevenDaysGiveNumber()
    {
        return self::getWebActivitySetting('seven_days_give_number');
    }

    /**
     * 邀请好友活动开启标记
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getInviteFlg()
    {
        return self::getWebActivitySetting('invite_flg');
    }

    /**
     * 每邀请多少好友
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getInviteStageNumber()
    {
        return self::getWebActivitySetting('invite_stage_number');
    }

    /**
     * 每邀请多少好友赠送类型
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getInviteStageGiveType()
    {
        return self::getWebActivitySetting('invite_stage_give_type');
    }

    /**
     * 每邀请多少好友赠送VIP天数或者积分数量
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getInviteStageGiveNumber()
    {
        return self::getWebActivitySetting('invite_stage_give_number');
    }

    /**
     * 累计邀多少好友
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getInviteTotalNumber()
    {
        return self::getWebActivitySetting('invite_total_number');
    }

    /**
     * 累计邀多少好友赠送类型
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getInviteTotalGiveType()
    {
        return self::getWebActivitySetting('invite_total_give_type');
    }

    /**
     * 累计邀多少好友赠送VIP天数或者积分数量
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getInviteTotalGiveNumber()
    {
        return self::getWebActivitySetting('invite_total_give_number');
    }

    /**
     * IP去重开启标志
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getIpRepeatFlg()
    {
        return self::getWebActivitySetting('ip_repeat_flg');
    }

    /**
     * 获取配置信息
     * @param null $name
     * @return array|int
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getWebActivitySetting($name = null)
    {
        $redis_key = Redis::REDIS_KEY_WEB_ACTIVITY_SETTING_DETAIL;
        if (Redis::getInstance()->exists($redis_key)) {
            $setting_array = Redis::getInstance()->hGetAll($redis_key);
        }
        if (empty($setting_array)) {
            $setting = self::find();
            $setting_array = empty($setting) ? [] : $setting->toArray();
            if (!empty($setting_array)) {
                Redis::getInstance()->hMSet($redis_key, $setting_array);
                Redis::getInstance()->expire($redis_key, Redis::REDIS_KEY_EXPIRE_ONE_MONTH);
            }
        }
        if ($name) {
            if (isset($setting_array[$name])) {
                return $setting_array[$name] ?: 0;
            }
            return 0;
        }
        return $setting_array;
    }

}
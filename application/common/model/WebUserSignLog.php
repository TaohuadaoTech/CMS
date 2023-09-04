<?php


namespace app\common\model;


use think\Model;

class WebUserSignLog extends Model
{

    // 当前模型名称
    protected $name = 'web_user_sign_log';

    /**
     * 用户签到
     * @param $user_id
     * @return int|string
     */
    public static function addWebUserSignLog($user_id)
    {
        return self::insertGetId(['user_id' => $user_id, 'create_time' => time()]);
    }

    /**
     * 获取用户今日签到数据
     * @param $user_id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebUserSignLogByToday($user_id)
    {
        return self::where('user_id', $user_id)->whereBetween('create_time', [strtotime(date('Y-m-d')), time()])->find();
    }

    /**
     * 获取用户昨天签到数据
     * @param $user_id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebUserSignLogByYesterday($user_id)
    {
        $time = strtotime(date('Y-m-d'));
        return self::where('user_id', $user_id)->whereBetween('create_time', [strtotime('-1 day', $time), $time - 1])->find();
    }

    /**
     * 获取最近七天的签到信息
     * @param $user
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function findWebUserSignLogByUserLastSevenDays($user)
    {
        $difference = 1;
        $log_date_array = [];
        // 今日是否签到
        if (empty(self::findWebUserSignLogByToday($user->id))) {
            $difference = 0;
        }
        // 获取签到信息
        $table_naem = config('database.prefix') . $this->name;
        $day_number = max((($user->successive_sign_days - $difference) % 7), 0);
        $min_create_time = strtotime("-{$day_number} day", strtotime(date('Y-m-d', time())));
        $log_array = $this->query("SELECT * FROM {$table_naem} WHERE user_id = {$user->id} AND create_time >= {$min_create_time}");
        foreach ($log_array as $log) {
            $log_date_array[] = date('Ymd', $log['create_time']);
        }
        // 提取出连续签到记录
        $continuity_log_array = [];
        for ($index = 0; $index <= 6; $index++) {
            $day = date('Ymd', strtotime("-{$index} day"));
            if (in_array($day, $log_date_array)) {
                $continuity_log_array[] = $day;
            } else if ($index != 0) {
                break;
            }
        }
        return array_reverse($continuity_log_array);
    }

}
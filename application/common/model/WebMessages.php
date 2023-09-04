<?php


namespace app\common\model;


use app\common\library\Redis;
use think\Model;

class WebMessages extends Model
{

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    const READ_FLG_NO = 0;
    const READ_FLG_YES = 1;

    // 当前模型名称
    protected $name = 'web_messages';

    /**
     * 获取最新得公告消息
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findLastWebMessageInfo()
    {
        return self::where('status', self::STATUS_ENABLE)->order('create_time', 'DESC')->find();
    }

    /**
     * 分页获取所有得公告消息
     * @param $pn
     * @param $limit
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function findWebMessagesPage($pn, $limit)
    {
        return self::where('status', self::STATUS_ENABLE)->order(['create_time' => 'DESC'])->paginate(['page' => $pn, 'list_rows' => $limit]);
    }

    /**
     * 获取消息数量
     * @return int|string
     * @throws \think\Exception
     */
    public static function findAllWebMessagesCount()
    {
        return self::where('status', self::STATUS_ENABLE)->count();
    }

}
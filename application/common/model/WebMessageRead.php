<?php


namespace app\common\model;


use app\common\library\Bitmap;
use think\Model;

class WebMessageRead extends Model
{

    // 当前模型名称
    protected $name = 'web_message_read';

    /**
     * 添加已读记录
     * @param $user_id
     * @param $message_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addWebMessageRead($user_id, $message_id)
    {
        $read = self::findWebMessageReadByUserId($user_id);
        if (empty($read)) {
            $message_bitmap = (new Bitmap())->set($message_id)->getBitmap();
            self::insert([
                'user_id' => $user_id,
                'message_bitmap' => $message_bitmap,
                'create_time' => time()
            ]);
        } else {
            $bitmap = new Bitmap($read->message_bitmap);
            if (!$bitmap->exist($message_id)) {
                $message_bitmap = $bitmap->set($message_id)->getBitmap();
                self::where('id', $read->id)->setField([
                    'message_bitmap' => $message_bitmap,
                    'update_time' => time()
                ]);
            }
        }
    }

    /**
     * 获取用户公告消息已读记录
     * @param $user_id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebMessageReadByUserId($user_id)
    {
        return self::where('user_id', $user_id)->find();
    }

    /**
     * 检查消息已读状态
     * @param $user_id
     * @param $message_array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function checkWebMessageReadFlg($user_id, &$message_array)
    {
        $bitmap = null;
        $read = self::where('user_id', $user_id)->find();
        if ($read) {
            $bitmap = new Bitmap($read->message_bitmap);
        }
        foreach ($message_array as &$message) {
            if ($bitmap && $bitmap->exist($message->id)) {
                $message->read_flg = WebMessages::READ_FLG_YES;
            } else {
                $message->read_flg = WebMessages::READ_FLG_NO;
            }
        }
    }

}
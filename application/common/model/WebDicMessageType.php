<?php


namespace app\common\model;


use think\Model;

class WebDicMessageType extends Model
{

    // 当前模型名称
    protected $name = 'web_dic_message_type';

    /**
     * 添加分类
     * @param $type_name
     * @return int|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addWebDicMessageType($type_name)
    {
        $type = self::where('name', $type_name)->find();
        if (empty($type)) {
            return self::insertGetId([
                'name' => $type_name,
                'create_time' => time()
            ]);
        }
        return $type->id;
    }

}
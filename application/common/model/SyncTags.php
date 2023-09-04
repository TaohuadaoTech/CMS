<?php


namespace app\common\model;


use think\Model;

class SyncTags extends Model
{

    // 当前模型名称
    protected $name = 'sync_tags';

    /**
     * 同步标签
     * @param $tag_id
     * @param $tag_name
     */
    public function sync($tag_id, $tag_name)
    {
        try {
            $this->isUpdate(false);
            self::save(['id' => $tag_id, 'name' => $tag_name, 'created_at' => date('Y-m-d H:i:s')]);
        } catch (\Exception $ex) {
            // 异常表示标签已存在
        }
    }

    /**
     * 通过名称模糊匹配标签信息
     * @param $name
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findSyncTagsByLikeName($name, $limit = 5)
    {
        return self::whereOr('name', 'LIKE', "{$name}%")->limit($limit)->select();
    }

}
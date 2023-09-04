<?php


namespace app\common\model;


use think\Model;

class SyncActresses extends Model
{

    // 当前模型名称
    protected $name = 'sync_actresses';

    /**
     * 同步数据
     * @param $actress
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sync($actress)
    {
        if (empty($this->field)) {
            $this->field = $this->getQuery()->getTableInfo('', 'fields');
        }
        $database_data = $this->find($actress['id']);
        if (empty($database_data)) {
            $this->isUpdate(false);
            $this->save($actress);
        } else {
            $allowFields = $this->checkAllowField();
            foreach ($actress as $key => $value) {
                if (!in_array($key, $allowFields)) {
                    unset($actress[$key]);
                }
            }
            unset($actress['id']);
            $this->where('id', $database_data->id)->setField($actress);
        }
    }

    /**
     * 通过姓名模糊匹配演员信息
     * @param $name
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findSyncActressesByLikeName($name, $limit = 5)
    {
        return self::whereOr('chinese_name', 'LIKE', "{$name}%")->whereOr('japanese_name', 'LIKE', "{$name}%")->whereOr('english_name', 'LIKE', "{$name}%")->limit($limit)->select();
    }

}
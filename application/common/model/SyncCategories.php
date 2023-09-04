<?php


namespace app\common\model;


use think\Model;

class SyncCategories extends Model
{

    // 当前模型名称
    protected $name = 'sync_categories';

    public function getTableName()
    {
        return $this->name;
    }

    /**
     * 同步数据
     * @param $categorie
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sync($categorie)
    {
        if (empty($this->field)) {
            $this->field = $this->getQuery()->getTableInfo('', 'fields');
        }
        $database_data = $this->find($categorie['id']);
        if (empty($database_data)) {
            $this->isUpdate(false);
            $this->save($categorie);
        } else {
            $allowFields = $this->checkAllowField();
            foreach ($categorie as $key => $value) {
                if (!in_array($key, $allowFields)) {
                    unset($categorie[$key]);
                }
            }
            unset($categorie['id']);
            $this->where('id', $database_data->id)->setField($categorie);
        }
    }

}
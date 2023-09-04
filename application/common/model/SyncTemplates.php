<?php


namespace app\common\model;


use think\Model;

class SyncTemplates extends Model
{

    // 当前模型名称
    protected $name = 'sync_templates';

    /**
     * 同步数据
     * @param $template
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sync($template)
    {
        if (empty($this->field)) {
            $this->field = $this->getQuery()->getTableInfo('', 'fields');
        }
        $database_data = $this->find($template['id']);
        if (empty($database_data)) {
            $this->isUpdate(false);
            $this->save($template);
        } else {
            $allowFields = $this->checkAllowField();
            foreach ($template as $key => $value) {
                if (!in_array($key, $allowFields)) {
                    unset($template[$key]);
                }
            }
            unset($template['id']);
            $this->where('id', $database_data->id)->setField($template);
        }
    }

}
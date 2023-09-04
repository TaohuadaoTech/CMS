<?php


namespace app\common\model;


use app\common\library\Redis;
use think\Model;

class WebCategorys extends Model
{

    const MODE_FREE = 0;
    const MODE_VIP = 1;
    const MODE_REFLECT = 2;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    // 当前模型名称
    protected $name = 'web_categorys';

    /**
     * 获取所有的一级分类
     * @param null $ignore_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBelongCategorysOneLevel($ignore_id = null)
    {
        $where = self::where('belong_to', 0);
        if ($ignore_id) {
            $where->where('id', '<>', $ignore_id);
        }
        return $where->field('id, name')->select();
    }

    /**
     * 获取所有的二级分类
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBelongCategorysTwoLevel()
    {
        return self::where('belong_to', '<>', 0)->select();
    }

    /**
     * 获取分类
     * @param int $limit
     * @param bool $child
     * @param null $sync_category_id
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function findWebCategorys($limit = 0, $child = false, $sync_category_id = null)
    {
        $syncCategoriesModel = new SyncCategories();
        $sql =  "SELECT web.*, sync.id AS sync_id, sync.videos_bitmap ";
        $sql .= "FROM " . config('database.prefix') . $this->name . " web ";
        $sql .= "LEFT JOIN " . config('database.prefix') . $syncCategoriesModel->getTableName() . " sync ON (web.map_to = sync.id) ";
        $sql .= "WHERE web.status = " . self::STATUS_ENABLE . ($child ? " AND belong_to <> 0 " : " ");
        if ($sync_category_id) {
            $sql .= "AND sync.id = {$sync_category_id} ";
        }
        $sql .= "ORDER BY web.`index` ASC ";
        if ($limit) {
            $sql .= "LIMIT {$limit}";
        }
        return $this->query($sql);
    }

    /**
     * 获取分类（使用缓存）
     * @param bool $read_cache
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function findWebCategorysByCache($read_cache = true)
    {
        $redis_value = $read_cache ? Redis::getInstance()->get(Redis::REDIS_KEY_WEB_CATEGORY_INFO) : '';
        if (empty($redis_value)) {
            $categorys = $this->findWebCategorys();
            $redis_value = json_encode($categorys, JSON_UNESCAPED_UNICODE);
            Redis::getInstance()->set(Redis::REDIS_KEY_WEB_CATEGORY_INFO, $redis_value);
        }
        return json_decode($redis_value, true);
    }

    /**
     * 按照父类ID删除分类
     * @param $belong_to
     */
    public static function deleteWebCategorysByBelongTo($belong_to) {
        self::where('belong_to', $belong_to)->delete();
    }

    /**
     * 按照父类ID修改状态
     * @param $belong_to
     * @param $values
     */
    public static function multiWebCategorysByBelongTo($belong_to, $values) {
        self::where('belong_to', $belong_to)->setField($values);
    }

}
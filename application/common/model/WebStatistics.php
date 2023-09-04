<?php


namespace app\common\model;


use think\Model;

class WebStatistics extends Model
{

    const SHOW_TYPE_HOUR = 'hour';
    const SHOW_TYPE_DAY = 'day';
    const SHOW_TYPE_MONTH = 'month';

    // 当前模型名称
    protected $name = 'web_statistics';

    /**
     * 添加统计数据
     * @param $site_id
     * @param $start_time
     * @param $end_time
     * @param $data
     * @return int|string
     */
    public function addWebStatistics($site_id, $start_time, $end_time, $data)
    {
        $difference = $end_time - $start_time;
        $create_time = $start_time + intval(($difference / 2));
        $data['site_id'] = $site_id;
        $data['start_time'] = $start_time;
        $data['end_time'] = $end_time;
        $data['create_time'] = $create_time;
        return $this->insert($data);
    }

    /**
     * 修改数据
     * @param $id
     * @param $data
     * @return int
     */
    public function updateWebStatistics($id, $data)
    {
        return $this->where('id', $id)->setField($data);
    }

    /**
     * 获取符合条件的统计数据
     * @param $site_id
     * @param $start_time
     * @param $end_time
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function findWebStatisticsOneByCreateTime($site_id, $start_time, $end_time)
    {
        return $this->where('site_id', $site_id)->whereBetween('create_time', [$start_time, $end_time])->find();
    }

    /**
     * 获取统计数据
     * @param $site_id
     * @param $start_time
     * @param $end_time
     * @param $show_type
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function findWebStatisticsByCount($site_id, $start_time, $end_time, $show_type)
    {
        switch ($show_type) {
            case self::SHOW_TYPE_HOUR:
                $select = "FROM_UNIXTIME(create_time, '%H') AS category ";
                $group = "GROUP BY FROM_UNIXTIME(create_time, '%H') ";
                break;
            case self::SHOW_TYPE_DAY:
                $select = "FROM_UNIXTIME(create_time, '%m%d') AS category ";
                $group = "GROUP BY FROM_UNIXTIME(create_time, '%m%d') ";
                break;
            case self::SHOW_TYPE_MONTH:
                $select = "FROM_UNIXTIME(create_time, '%Y%m') AS category ";
                $group = "GROUP BY FROM_UNIXTIME(create_time, '%Y%m') ";
                break;
        }
        $table_name = config('database.prefix') . $this->name;
        $find_where = "WHERE (create_time BETWEEN {$start_time} AND {$end_time}) ";
        if ($site_id && $site_id != '-1') {
            $find_where .= " AND site_id = {$site_id} ";
        }
        return $this->query("SELECT SUM(pv) AS pv, SUM(uv) AS uv, SUM(rv) AS rv, SUM(vv) AS vv, SUM(mv) AS mv, {$select} FROM {$table_name} {$find_where} {$group}");
    }

}
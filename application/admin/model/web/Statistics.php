<?php

namespace app\admin\model\web;

use app\common\model\WebStatistics;
use think\Model;


class Statistics extends Model
{

    

    

    // 表名
    protected $name = 'web_statistics';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'start_time_text',
        'end_time_text',
        'create_time_text'
    ];
    

    
    public function getTypeList()
    {
        return ['pv' => __('Pv'), 'uv' => __('Uv'), 'rv' => __('Rv'), 'vv' => __('Vv'), 'mv' => __('Mv')];
    }

    public function getAllShowType($type, $start_time, $end_time, $today = false)
    {
        $result_array = [];
        switch ($type) {
            case WebStatistics::SHOW_TYPE_HOUR:
                if ($today) {
                    $current_hour = date('H');
                    for ($hour = 0; $hour <= $current_hour; $hour++) {
                        $result_array[] = str_pad($hour, 2, '0', STR_PAD_LEFT);
                    }
                } else {
                    for ($hour = 0; $hour < 24; $hour++) {
                        $result_array[] = str_pad($hour, 2, '0', STR_PAD_LEFT);
                    }
                }
                break;
            case WebStatistics::SHOW_TYPE_DAY:
                while ($start_time <= $end_time) {
                    $result_array[] = date('md', $start_time);
                    $start_time += 86400;
                }
                break;
            case WebStatistics::SHOW_TYPE_MONTH:
                $start_time = strtotime(date('Y-m', $start_time));
                while ($start_time <= $end_time) {
                    $result_array[] = date('Ym', $start_time);
                    $start_time = strtotime('+1 month', $start_time);
                }
                break;
        }
        return $result_array;
    }

    public function getStartTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['start_time']) ? $data['start_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['end_time']) ? $data['end_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['create_time']) ? $data['create_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setStartTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setCreateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}

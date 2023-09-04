<?php

namespace app\admin\model\web;

use think\Model;


class Categorys extends Model
{

    

    

    // 表名
    protected $name = 'web_categorys';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'pid',
        'status_text',
        'create_time_text',
        'update_time_text'
    ];

    public function getStatusList()
    {
        return ['1' => __('Status_Enable'), '0' => __('Status_Disable')];
    }

    public function getModelList()
    {
        return ['0' => __('Mode_free'), '1' => __('Mode_vip'), '2' => __('Mode_reflect')];
    }

    public function getPidAttr($value, $data)
    {
        return $value ? $value : (isset($data['belong_to']) ? $data['belong_to'] : '');
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['create_time']) ? $data['create_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getUpdateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['update_time']) ? $data['update_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCreateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setUpdateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

}

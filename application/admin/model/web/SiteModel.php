<?php

namespace app\admin\model\web;

use think\Model;


class SiteModel extends Model
{

    

    

    // 表名
    protected $name = 'web_model_download';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'model_status_text',
        'create_time_text'
    ];
    

    
    public function getModelStatusList()
    {
        return ['1' => __('Status_Enable'), '0' => __('Status_Disable')];
    }

    public function getModelStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['model_status']) ? $data['model_status'] : '');
        $list = $this->getModelStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['create_time']) ? $data['create_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCreateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}

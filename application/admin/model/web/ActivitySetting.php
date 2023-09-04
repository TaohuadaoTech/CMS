<?php

namespace app\admin\model\web;

use think\Model;


class ActivitySetting extends Model
{

    

    

    // 表名
    protected $name = 'web_activity_setting';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'sign_flg_text',
        'continuity_give_type_text',
        'seven_days_give_type_text',
        'invite_flg_text',
        'invite_stage_give_type_text',
        'invite_total_give_type_text',
        'ip_repeat_flg_text',
        'create_time_text',
        'update_time_text'
    ];
    

    public function getSignFlgList()
    {
        return ['1' => __('Enable'), '0' => __('Disable')];
    }

    public function getContinuityGiveTypeList()
    {
        return ['1' => __('Vip'), '2' => __('Integral')];
    }

    public function getSevenDaysGiveTypeList()
    {
        return ['1' => __('Vip'), '2' => __('Integral')];
    }

    public function getInviteFlgList()
    {
        return ['1' => __('Enable'), '0' => __('Disable')];
    }

    public function getInviteStageGiveTypeList()
    {
        return ['1' => __('Vip'), '2' => __('Integral')];
    }

    public function getInviteTotalGiveTypeList()
    {
        return ['1' => __('Vip'), '2' => __('Integral')];
    }

    public function getIpRepeatFlgList()
    {
        return ['1' => __('Enable'), '0' => __('Disable')];
    }

    public function getSignFlgTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['sign_flg']) ? $data['sign_flg'] : '');
        $list = $this->getSignFlgList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getContinuityGiveTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['continuity_give_type']) ? $data['continuity_give_type'] : '');
        $list = $this->getContinuityGiveTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getSevenDaysGiveTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['seven_days_give_type']) ? $data['seven_days_give_type'] : '');
        $list = $this->getSevenDaysGiveTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getInviteFlgTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['invite_flg']) ? $data['invite_flg'] : '');
        $list = $this->getInviteFlgList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getInviteStageGiveTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['invite_stage_give_type']) ? $data['invite_stage_give_type'] : '');
        $list = $this->getInviteStageGiveTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getInviteTotalGiveTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['invite_total_give_type']) ? $data['invite_total_give_type'] : '');
        $list = $this->getInviteTotalGiveTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getIpRepeatFlgTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['ip_repeat_flg']) ? $data['ip_repeat_flg'] : '');
        $list = $this->getIpRepeatFlgList();
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

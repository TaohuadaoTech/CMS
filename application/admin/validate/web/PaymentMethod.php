<?php

namespace app\admin\validate\web;

use think\Validate;

class PaymentMethod extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'name' => 'require',
        'channel' => 'require',
        'pay_param_one' => 'require',
        'pay_param_two' => 'require',
        'pay_param_three' => 'require',
        'qrcode_image' => 'require'
    ];

    /**
     * 字段描述
     */
    protected $field = [
    ];
    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
        'auto' => ['name', 'channel', 'pay_param_one', 'pay_param_two', 'pay_param_three'],
        'manually' => ['channel', 'channel', 'qrcode_image'],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'name' => __('Name'),
            'channel' => __('Channel'),
            'pay_param_one' => __('Pay_param_one'),
            'pay_param_two' => __('Pay_param_two'),
            'pay_param_three' => __('Pay_param_three'),
            'qrcode_image' => __('Qrcode_image')
        ];
        parent::__construct($rules, $message, $field);
    }

}

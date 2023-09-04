<?php


namespace app\common\model;


use think\Model;

class WebPaymentMethod extends Model
{

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    const CHANNEL_ALIPAY = 'Alipay';
    const CHANNEL_WECHATPAY = 'Wechatpay';
    const CHANNEL_EPAY = 'Epay';
    const CHANNEL_MANUALLY = 'Manual';

    // 当前模型名称
    protected $name = 'web_payment_method';

    /**
     * 获取所有支持的支付类型
     * @return array
     */
    public static function getAllChannel()
    {
        return ['Alipay' => self::CHANNEL_ALIPAY, 'Wechatpay' => self::CHANNEL_WECHATPAY, 'Epay' => self::CHANNEL_EPAY, 'Manual' => self::CHANNEL_MANUALLY];
    }

    /**
     * 获取所有的支付方式
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findWebPaymentMethod()
    {
        $payment_method_array = self::where('status', self::STATUS_ENABLE)->order('index')->select();
        foreach ($payment_method_array as &$payment_method) {
            switch ($payment_method->channel) {
                case WebPaymentMethod::CHANNEL_ALIPAY:
                    $default_image_name = 'alipay';
                    break;
                case WebPaymentMethod::CHANNEL_WECHATPAY:
                    $default_image_name = 'wechat';
                    break;
                case WebPaymentMethod::CHANNEL_EPAY:
                    $default_image_name = 'caihong';
                    break;
                case WebPaymentMethod::CHANNEL_MANUALLY:
                    $default_image_name = 'manual';
                    break;
            }
            $payment_method['defaultImageName'] = $default_image_name;
        }
        return $payment_method_array;
    }

}
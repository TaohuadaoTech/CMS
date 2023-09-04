<?php


namespace app\common\model;


use think\Model;

class WebOrders extends Model
{

    const TYPE_VIP = 1;
    const TYPE_INTEGRAL = 2;

    const PAY_STATUS_SUCCESS = 1;
    const PAY_STATUS_WAIT = 2;
    const PAY_STATUS_FAIL = 3;
    const PAY_STATUS_TIMEOUT = 4;
    const PAY_STATUS_UNKNOWN = 5;

    // 当前模型名称
    protected $name = 'web_orders';

    public static function getPayStatusList()
    {
        return [self::PAY_STATUS_SUCCESS => __('Pay_status_success'), self::PAY_STATUS_WAIT => __('Pay_status_wait'), self::PAY_STATUS_FAIL => __('Pay_status_fail'),
                self::PAY_STATUS_TIMEOUT => __('Pay_status_timeout'), self::PAY_STATUS_UNKNOWN => __('Pay_status_unknown')];
    }

    /**
     * 创建订单
     * @param $data
     * @return int|string
     */
    public static function addWebOrders($data)
    {
        return self::insertGetId($data);
    }

    /**
     * 获取订单信息
     * @param $user_id
     * @param $pay_status
     * @param $order_sn
     * @param $start_time
     * @param $end_time
     * @param $pn
     * @param $page_size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function findWebOrdersByPage($user_id, $pay_status, $order_sn, $start_time, $end_time, $pn, $page_size)
    {
        $where = self::where('user_id', $user_id);
        if ($pay_status) {
            $where->where('pay_status', $pay_status);
        }
        if ($order_sn) {
            $where->whereLike('order_sn', "%{$order_sn}%");
        }
        if ($start_time && $end_time) {
            $where->whereBetween('create_time', [strtotime($start_time), strtotime($end_time)]);
        }
        return $where->order('create_time', 'DESC')->paginate(['page' => $pn, 'list_rows' => $page_size]);
    }

}
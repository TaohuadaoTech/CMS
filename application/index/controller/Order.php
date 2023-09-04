<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\model\WebChargeIntegral;
use app\common\model\WebOrders;
use app\common\model\WebPaymentMethod;
use app\common\model\WebVipPackages;

class Order extends Frontend
{

    protected $showTopAdvertising = false;
    protected $showBottomAdvertising = false;
    protected $showLeftAdvertising = false;
    protected $showRightAdvertising = false;

    /**
     * VIP购买列表
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function viplist()
    {
        $this->view->assign('paymentMethodArray', WebPaymentMethod::findWebPaymentMethod());
        $this->view->assign('vipPackages', WebVipPackages::findWebVipPackages());
        $this->assignconfig('channelManually', WebPaymentMethod::CHANNEL_MANUALLY);
        $this->assignconfig('payType', WebOrders::TYPE_VIP);
        return $this->view->fetch();
    }

    /**
     * 积分购买列表
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function scorelist()
    {
        $this->view->assign('paymentMethodArray', WebPaymentMethod::findWebPaymentMethod());
        $this->view->assign('chargeIntegralArray', WebChargeIntegral::findWebChargeIntegral());
        $this->assignconfig('channelManually', WebPaymentMethod::CHANNEL_MANUALLY);
        $this->assignconfig('payType', WebOrders::TYPE_INTEGRAL);
        return $this->view->fetch();
    }

    /**
     * 提交支付订单
     */
    public function submit()
    {
        $user = $this->auth->getUser();
        $site_id = $this->request->post('siteId');
        $pay_type = $this->request->post('type');
        $channel = $this->request->post('channel');
        $integral_id = $this->request->post('integralId');
        $payment_method_id = $this->request->post('paymentMethodId');
        if (WebPaymentMethod::CHANNEL_MANUALLY == $channel) {
            $order_sn = create_order_sn();
            // 手动支付直接创建订单
            $data = [
                'site_id' => $site_id,
                'order_sn' => $order_sn,
                'type' => $pay_type,
                'user_id' => $user->id,
                'businessid' => $integral_id,
                'pay_method_id' => $payment_method_id,
                'pay_status' => WebOrders::PAY_STATUS_UNKNOWN,
                'create_time' => time()
            ];
            WebOrders::addWebOrders($data);
        } else {
            $this->error(__('Pay_not_support'));
        }
        $this->success();
    }

    /**
     * 订单列表
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function datalist()
    {
        $user = $this->auth->getUser();
        // 获取参数
        $pay_status = $this->getUrlPathParams(0) ?: 0;
        $order_sn = $this->getUrlPathParams(1) ?: 0;
        $start_time = $this->getUrlPathParams(2) ?: 0;
        $end_time = $this->getUrlPathParams(3) ?: 0;
        $pn = $this->getUrlPathParamsInt(4) ?: 1;
        $limit = $this->getUrlPathParamsInt(5) ?: 15;
        // 查询符合条件得订单信息
        $payment_method_map_array = [];
        $payment_method_array = WebPaymentMethod::all();
        $pay_status_list = WebOrders::getPayStatusList();
        $page = WebOrders::findWebOrdersByPage($user->id, $pay_status, $order_sn, $start_time, $end_time, $pn, $limit);
        $order_array = $page->items();
        foreach ($payment_method_array as $payment) {
            $payment_method_map_array[$payment['id']] = $payment;
        }
        foreach ($order_array as &$order) {
            $order['username'] = $user->username;
            $order['pay_status_str'] = $pay_status_list[$order->pay_status];
            $order['create_time_at'] = date('Y-m-d H:i:s', $order->create_time);
            if (WebOrders::TYPE_VIP == $order->type) {
                $vip = WebVipPackages::find($order->businessid);
                $order['amount'] = $vip->getAttr('sale_price');
                $order['name'] = $vip->getAttr('name') . 'VIP';
            } else {
                $integral = WebChargeIntegral::find($order->businessid);
                $order['amount'] = $integral->getAttr('amount');
                $order['name'] = $integral->getAttr('name');
            }
            switch ($order->pay_status) {
                case WebOrders::PAY_STATUS_SUCCESS:
                    $classname = 'succeed';
                    break;
                case WebOrders::PAY_STATUS_WAIT:
                    $classname = 'waiting-paid';
                    break;
                case WebOrders::PAY_STATUS_FAIL:
                    $classname = 'failed';
                    break;
                case WebOrders::PAY_STATUS_TIMEOUT:
                    $classname = 'timeout';
                    break;
                default:
                    $classname = 'unknown';
                    break;
            }
            $order['classname'] = $classname;
            $order['channel'] = isset($payment_method_map_array[$order->pay_method_id]) ? $payment_method_map_array[$order->pay_method_id]['name'] : '';
        }
        // 获取分页信息
        $page_html = $this->getPageHtml($pn, $limit, $page->total(), "/index/order/datalist/{$pay_status}/{$order_sn}/{$start_time}/{$end_time}");
        $this->view->assign('orderInfo', ['orderArray' => $order_array, 'pageHtml' => $page_html]);
        $this->view->assign('params', [
            'payStatus' => $pay_status,
            'orderSn' => $order_sn ?: null,
            'startTime' => $start_time ?: null,
            'endTime' => $end_time ?: null
        ]);
        $this->view->assign('payStatusArray', $pay_status_list);
        return $this->view->fetch();
    }

}
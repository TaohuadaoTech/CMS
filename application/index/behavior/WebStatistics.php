<?php

namespace app\index\behavior;

use app\common\library\Auth;
use app\common\library\Logger;
use app\common\library\Redis;
use app\common\model\WebChargeIntegral;
use app\common\model\WebOrders;
use app\common\model\WebVipPackages;
use DateTime;

class WebStatistics
{

    protected $auth;
    protected $request;

    // 同步间隔时间
    private $sync_interval_time = 600;

    public function run(&$params)
    {
        register_shutdown_function(function () {
            try {
                $this->webStatisticsLog();
            } catch (\Throwable $ex) {
                Logger::error('WebStatistics::run => 记录访问信息异常: ' . $ex->getMessage());
                Logger::error($ex->getTraceAsString());
            }
        });
    }

    /**
     * 记录访问数据
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    private function webStatisticsLog()
    {
        $date = date('ymdH');
        $not_up_database = true;
        $this->request = request();
        $this->auth = Auth::instance();
        $site = $this->getCurrentSite();
        if (empty($site)) {
            return;
        }
        $request_uri = $this->auth->getRequestUri();
        // 使用Redis暂存数据，KEY格式为：前缀:ymdH:siteId
        $redis = Redis::getInstance();
        $redis_key = Redis::REDIS_KEY_SITE_STATISTICS_KEY_INFO . $date . ':' . $site['id'];
        $redis_key_uv = Redis::REDIS_KEY_SITE_STATISTICS_KEY_UV . $date . ':' . $site['id'];
        switch ($request_uri) {
            case 'index/index':
                // 是首页
                break;
            case 'user/register':
                // 是注册请求
                $redis->hIncrBy($redis_key, 'rv', 1);
                break;
            case 'm3u8/video':
                // 是播放请求
                $redis->hIncrBy($redis_key, 'vv', 1);
                break;
            case 'order/submit':
                // 是订单支付请求
                if ($this->request->isPost()) {
                    $amount = 0;
                    $params = $this->getParams();
                    $integral_id = $params['integralId'];
                    if ($params['type'] == WebOrders::TYPE_VIP) {
                        $vip_packages = WebVipPackages::find($integral_id);
                        $amount = $vip_packages['sale_price'];

                    } else if ($params['type'] == WebOrders::TYPE_INTEGRAL) {
                        $charge_integral = WebChargeIntegral::find($integral_id);
                        $amount = $charge_integral['amount'];
                    }
                    $redis->hIncrByFloat($redis_key, 'mv', $amount);
                }
                break;
            default:
                $not_up_database = false;
                break;
        }
        // 其它请求
        $redis->hIncrBy($redis_key, 'pv', 1);
        // 统计访客数
        if (!$this->auth->isLogin()) {
            $ip = get_user_ip() ?: $this->request->ip();
            $redis->pfAdd($redis_key_uv, array($ip));
        }
        // 特殊请求和POST请求不更新访问数据
        if ($not_up_database || (!$this->request->isGet())) {
            return;
        }
        // 加锁，一个时间间隔中只更新一次
        if ($redis->set(Redis::REDIS_KEY_SITE_STATISTICS_SYNC_LOCK, time(), ['nx', 'ex' => $this->sync_interval_time])) {
            $iterator = null;
            $number_data_map = [];
            $delete_redis_key_array = [];
            while (false !== ($keys = $redis->scan($iterator, (Redis::REDIS_KEY_SITE_STATISTICS_KEY_PREFIX . '*')))) {
                foreach ($keys as $key) {
                    $key_array = explode(":", $key);
                    $key_array_count = count($key_array);
                    $map_key = $key_array[$key_array_count - 2] . ":" . $key_array[$key_array_count - 1];
                    if (array_key_exists($map_key, $number_data_map)) {
                        $number_data = $number_data_map[$map_key];
                    } else {
                        $number_data = [];
                    }
                    if (strpos($key, 'info') !== false) {
                        $array1 = $number_data;
                        $array2 = $redis->hGetAll($key);
                        $number_data = array_reduce(array_keys($array1 + $array2), function ($carry, $key) use ($array1, $array2) {
                            $carry[$key] = ($array1[$key] ?? 0) + ($array2[$key] ?? 0);
                            return $carry;
                        });
                    } else if (strpos($key, 'uv') !== false) {
                        $uv = isset($number_data['uv']) ? $number_data['uv'] : 0;
                        $number_data['uv'] = $redis->pfCount($key) + $uv;
                    }
                    $number_data_map[$map_key] = $number_data;
                    // 不是当前时间的Redis Key进行清除
                    if (strpos($key, $date) === false) {
                        $delete_redis_key_array[] = $key;
                    }
                }
            }
            // 更新统计数据
            if (!empty($number_data_map)) {
                $webStatisticsModel = new \app\common\model\WebStatistics();
                foreach ($number_data_map as $key => $value) {
                    $key_array = explode(":", $key);
                    $start_time = DateTime::createFromFormat('ymdH', $key_array[0])->getTimestamp();
                    $end_time = DateTime::createFromFormat('ymdHis', ($key_array[0] . '5959'))->getTimestamp();
                    $staistics = $webStatisticsModel->findWebStatisticsOneByCreateTime($key_array[1], $start_time, $end_time);
                    if (empty($staistics)) {
                        $webStatisticsModel->addWebStatistics($key_array[1], $start_time, $end_time, $value);
                    } else {
                        $webStatisticsModel->updateWebStatistics($staistics['id'], $value);
                    }
                }
            }
            // 删除过时的缓存数据
            if (!empty($delete_redis_key_array)) {
                $redis->del($delete_redis_key_array);
            }
        }
    }

    /**
     * 获取站点信息
     * @return mixed
     */
    private function getCurrentSite()
    {
        $domain_name = $this->request->host(true);
        $redis_key = Redis::REDIS_KEY_WEB_SITE_TEMPLATE_PATH . $domain_name;
        return json_decode(Redis::getInstance()->get($redis_key), true);
    }

    /**
     * 获取所有请求参数
     * @return mixed
     */
    private function getParams()
    {
        return $this->request->param('', null, 'trim,strip_tags,htmlspecialchars');
    }

}
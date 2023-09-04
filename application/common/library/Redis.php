<?php


namespace app\common\library;


class Redis
{

    // ==================== Redis 超时时间 ======================== //
    const REDIS_KEY_EXPIRE_FIVE_MINUTES = 300; // 五分钟
    const REDIS_KEY_EXPIRE_TEN_MINUTES = 600; // 十分钟
    const REDIS_KEY_EXPIRE_THREE_HOURS = 10800; // 三小时
    const REDIS_KEY_EXPIRE_ONE_DAY = 86400; // 一天
    const REDIS_KEY_EXPIRE_TWO_DAYS = 172800; // 两天
    const REDIS_KEY_EXPIRE_SEVEN_DAYS = 604800; // 七天
    const REDIS_KEY_EXPIRE_ONE_MONTH = 2592000; // 一个月
    // ==================== Redis Key 配置 ======================= //
    const REDIS_KEY_UNIT_PREFIX = 'webmaster:'; // KEY 统一前缀
    const REDIS_KEY_WEB_SITE_TEMPLATE_PATH = self::REDIS_KEY_UNIT_PREFIX . 'web_site:template_path:'; // 网址模板路径配置(+domain_name)
    const REDIS_KEY_WEB_ACTIVITY_SETTING_DETAIL = self::REDIS_KEY_UNIT_PREFIX . 'web_activity_setting:detail'; // 活动配置信息
    const REDIS_KEY_WEB_ADVERTISEMENT_INFO_HASHMAP = self::REDIS_KEY_UNIT_PREFIX . 'web_advertisement:info:hashmap'; // 广告配置信息
    const REDIS_KEY_WEB_LINKS_INFO = self::REDIS_KEY_UNIT_PREFIX . 'web_links:info'; // 友链配置信息
    const REDIS_KEY_WEB_CATEGORY_INFO = self::REDIS_KEY_UNIT_PREFIX . 'web_category:info'; // 分类信息
    const REDIS_KEY_WEB_ORIGINS_MAP = self::REDIS_KEY_UNIT_PREFIX . 'web_origins:map'; // 视频原信息
    const REDIS_KEY_SYS_NOTICE_SYNC_MARK = self::REDIS_KEY_UNIT_PREFIX . 'system_notice:sync_mark'; // 系统通知同步标识
    const REDIS_KEY_TEMPLATE_SYNC_MARK = self::REDIS_KEY_UNIT_PREFIX . 'template:sync_mark'; // 模板同步标识
    const REDIS_KEY_VIDEO_SYNC_MARK = self::REDIS_KEY_UNIT_PREFIX . 'video:sync_mark'; // 视频同步标识
    const REDIS_KEY_VIDEO_SYNC_SUCCESS_INFO = self::REDIS_KEY_UNIT_PREFIX . 'video:sync_success:info'; // 视频同步成功消息队列
    const REDIS_KEY_VIDEO_SYNC_SUCCESS_MARK = self::REDIS_KEY_UNIT_PREFIX . 'video:sync_success:mark'; // 视频全部同步完成标识
    const REDIS_KEY_ORDER_SN_SEQUENCE_NUMBER = self::REDIS_KEY_UNIT_PREFIX . 'order_sn:sequence_number:'; // 订单序列号(+day)
    const REDIS_KEY_USER_REG_IP_RESTRICTION = self::REDIS_KEY_UNIT_PREFIX . 'user:reg:ip_restriction:'; // 用户注册IP限制(+ip)
    const REDIS_KEY_USER_REG_LIMIT_IP_BAN_TIME = self::REDIS_KEY_UNIT_PREFIX . 'user:reg:ip_ban_time:'; // 用户注册IP限制后封禁事件(+ip)
    const REDIS_KEY_RECENT_UPDATE_VIDEO_SET = self::REDIS_KEY_UNIT_PREFIX . 'recent_update:video:set'; // 最近更新视频信息
    const REDIS_KEY_RECENT_LAUNCHED_VIDEO_SET = self::REDIS_KEY_UNIT_PREFIX . 'recent_launched:video:set'; // 最近上市场视频信息
    const REDIS_KEY_TOP_TEN_VIDEO_SET = self::REDIS_KEY_UNIT_PREFIX . 'top_ten:video:set'; // TOP10视频信息
    const REDIS_KEY_YOU_LIKE_VIDEO_SET = self::REDIS_KEY_UNIT_PREFIX . 'you_like:video:set'; // 猜你喜欢视频信息

    const REDIS_KEY_SITE_STATISTICS_KEY_PREFIX = self::REDIS_KEY_UNIT_PREFIX . 'site:statistics:key:'; // 站点数据统计KEY前缀
    const REDIS_KEY_SITE_STATISTICS_SYNC_LOCK = self::REDIS_KEY_UNIT_PREFIX . 'site:statistics:sync_lock'; // 站点数据同步标记
    const REDIS_KEY_SITE_STATISTICS_KEY_INFO = self::REDIS_KEY_SITE_STATISTICS_KEY_PREFIX . 'info:'; // 站点数据统计信息(+date +site_id)
    const REDIS_KEY_SITE_STATISTICS_KEY_UV = self::REDIS_KEY_SITE_STATISTICS_KEY_PREFIX . 'uv:'; // 站点访客数据统计信息(+date +site_id)
    // ==================== Redus 静态配置结束 ==================== //

    private static $_instance;

    protected $options = [
        'host'        => '127.0.0.1',
        'port'        => 6379,
        'password'    => '',
        'select'      => 0,
        'timeout'     => 0,
        'persistent'  => false
    ];

    /**
     * Redis 实例
     * @var redis
     */
    public $handler;

    private function __construct($options = [])
    {
        $config_options = config('redis');
        if (!empty($config_options)) {
            $this->options = array_merge($this->options, $config_options);
        }
        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Redis;
        if ($this->options['persistent']) {
            $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
        } else {
            $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
        }
        if ('' != $this->options['password']) {
            $this->handler->auth($this->options['password']);
        }
        if (0 != $this->options['select']) {
            $this->handler->select($this->options['select']);
        }
    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance->handler;
    }

}
<?php


namespace app\api\controller;


use app\common\controller\Api;
use app\common\library\handle\ConfigurationHandle;
use app\common\library\Logger;
use app\common\library\SiteUtil;
use think\Db;
use think\Request;

class Site extends Api
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 针对Windows Server服务器初始化站点数据
     * @throws \think\Exception
     */
    public function initsite()
    {
        $domain = $this->request->host(true);
        // 获取数据库连接信息
        $cache_data = \think\Cache::pull(ConfigurationHandle::getBasicCacheKeyMysqlConnectData());
        if (empty($cache_data)) {
            return;
        }
        $data = json_decode($cache_data, true);
        // 验证请求来源
        $request_key = $this->request->get('key', '');
        $cache_key = isset($data['key']) ? $data['key'] : '';
        if (empty($request_key) || $request_key !== $cache_key) {
            Logger::error('Site::initsite => 请求初始化站点鉴权失败: ' . "{'request_key': '{$request_key}', 'cache_key': '{$cache_key}'}");
            return;
        }
        unset($data['key']);
        // 连接install命令中指定的数据库
        $instance = Db::connect($data);
        $instance->execute("SELECT 1");
        // 初始化站点数据
        SiteUtil::initSiteData($instance, $data['prefix'], $domain, 300);
    }

}
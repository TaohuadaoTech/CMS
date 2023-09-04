<?php


namespace app\common\library;


use app\common\library\handle\ConfigurationHandle;
use app\common\model\Config;
use app\common\model\SyncActresses;
use app\common\model\SyncCategories;
use app\common\model\SyncOrigins;
use app\common\model\SyncTags;
use app\common\model\SyncTemplates;
use app\common\model\SyncVideos;
use fast\Http;

class StandApi
{

    private static $_instance;

    private function __construct()
    {
        $api_host = ConfigurationHandle::getStandApiHost();
        $this->get_system_notice_url = $api_host . ConfigurationHandle::getStandGetSystemNoticeUri();
        $this->get_category_url = $api_host . ConfigurationHandle::getStandGetCategoryUri();
        $this->get_video_url = $api_host . ConfigurationHandle::getStandGetVideoUri();
        $this->get_template_url = $api_host . ConfigurationHandle::getStandGetTemplateUri();
        $this->get_origin_url = $api_host . ConfigurationHandle::getStandGetOriginUri();
        $this->get_instruction_url = $api_host . ConfigurationHandle::getStandGetInstructionUri();
    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private $get_system_notice_url;
    private $get_category_url;
    private $get_video_url;
    private $get_template_url;
    private $get_origin_url;
    private $get_instruction_url;

    private $limit = 1000;

    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * 同步模板
     * @param $offset
     * @param $limit
     * @return array|mixed
     */
    public function syncTemplate()
    {
        try {
            $pn = 1;
            $templatesModel = new SyncTemplates();
            do {
                $params = ['offset' => (($pn - 1) * $this->limit), 'limit' => $this->limit];
                $response_data = $this->verifyResponse($this->get($this->get_template_url, $params));
                $total_pn = $this->getTotalPn($response_data['total_count'], $this->limit);
                $templates = $response_data['templates'];
                foreach ($templates as $template) {
                    $templatesModel->sync($template);
                }
                $pn++;
            } while ($pn <= $total_pn);
            return true;
        } catch (\Exception $ex) {
            Logger::error("StandApi::syncTemplate => 同步模板失败: " . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
        return false;
    }

    /**
     * 同步视频源
     * @return bool
     */
    public function syncOrigin()
    {
        try {
            $time = time();
            Logger::write("StandApi::syncOrigin => 开始同步视频源");
            $response_data = $this->verifyResponse($this->get($this->get_origin_url));
            $categories = $response_data['categories'];
            if (!empty($categories)) {
                $originsModel = new SyncOrigins();
                foreach ($categories as $category) {
                    $originsModel->sync($category);
                }
            }
            Logger::write("StandApi::syncOrigin => 视频源信息同步完毕，共耗时: " . (time() - $time));
            return true;
        } catch (\Exception $ex) {
            Logger::error("StandApi::syncOrigin => 同步视频源失败: " . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
        return false;
    }

    /**
     * 同步分类数据
     * @return bool
     */
    public function syncCategory()
    {
        try {
            $time = time();
            Logger::write("StandApi::syncCategory => 开始同步分类信息");
            $response_data = $this->verifyResponse($this->get($this->get_category_url));
            $categories = $response_data['categories'];
            if (!empty($categories)) {
                $categoriesModel = new SyncCategories();
                foreach ($categories as $categorie) {
                    $categoriesModel->sync($categorie);
                }
            }
            Logger::write("StandApi::syncCategory => 分类信息同步完毕，共耗时: " . (time() - $time));
            return true;
        } catch (\Exception $ex) {
            Logger::error("StandApi::syncCategory => 同步分类数据失败: " . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
        return false;
    }

    /**
     * 单个同步视频
     * @param $video
     * @return bool
     */
    public function syncVideos($video)
    {
        if (empty($video)) {
            return false;
        }
        static $tagsModel = null;
        static $videosModel = null;
        static $actressesModel = null;
        if (empty($tagsModel)) {
            $tagsModel = new SyncTags();
        }
        if (empty($videosModel)) {
            $videosModel = new SyncVideos();
        }
        if (empty($actressesModel)) {
            $actressesModel = new SyncActresses();
        }
        try {
            // 同步视频标签
            $tags_name_array = explode(",", $video['tags']);
            $tags_id_array = explode(",", $video['tags_id']);
            $min_tag_size = min(count($tags_id_array), count($tags_name_array));
            for ($index = 0; $index < $min_tag_size; $index++) {
                $tagsModel->sync($tags_id_array[$index], $tags_name_array[$index]);
            }
            // 同步演员数据
            if (isset($video['actresses_info'])) {
                $actresses_info = $video['actresses_info'];
                foreach ($actresses_info as $actresses) {
                    $actressesModel->sync($actresses);
                }
            }
            // 同步视频数据
            $videosModel->sync($video);
            // 返回成功
            return true;
        } catch (\Exception $ex) {
            Logger::error('StandApi::syncVideos => 同步视频信息失败: ' . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
        return false;
    }

    /**
     * 获取视频信息
     * @param $max_id
     * @param $offset
     * @param $limit
     * @param int $with_actresses
     * @return array|bool
     */
    public function getVideos($max_id, $offset, $limit, $with_actresses = 0)
    {
        try {
            $params = ['offset' => $offset, 'limit' => $limit];
            if ($max_id) {
                $params['id'] = $max_id;
            }
            $params['with_actresses'] = $with_actresses;
            $response_data = $this->verifyResponse($this->get($this->get_video_url, $params));
            return ['rows' => $response_data['videos'], 'total' => $response_data['total_count']];
        } catch (\Exception $ex) {
            Logger::error('StandApi::getVideos => 获取视频数据失败: ' . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
        return false;
    }

    /**
     * 获取公告信息
     * @return bool|mixed
     */
    public function getSystemNotice()
    {
        try {
            $response = $this->get($this->get_system_notice_url);
            $response_data = $this->verifyResponse($response);
            $notice = $response_data['notice'];
            if ($notice) {
                Config::updateValueByName('system_notice', $notice);
            }
            return $notice;
        } catch (\Exception $ex) {
            Logger::error("StandApi::syncSystemNotice => 获取公告信息失败: " . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
        return false;
    }

    /**
     * 获取使用说明
     * @return array|mixed
     */
    public function getSystemInstruction()
    {
        try {
            return $this->verifyResponse($this->get($this->get_instruction_url));
        } catch (\Exception $ex) {
            Logger::error("StandApi::getSystemInstruction => 获取使用说明失败: " . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
        return [
            'url' => 'https://www.taohuadao.org',
            'title_main' => '欢迎使用桃花岛CMS建站系统',
            'title_sub' => '一键搭建视频网站 无需采集 立即播放 支持VIP打赏模式 数百套免费模板',
            'instruction' => '<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>欢迎使用桃花岛CMS建站系统，我们强烈建议您加入我们的群组，以便获取最佳技术支持服务！</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>Telegram：https://t.me/taohuadaoCMS</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>官方网站：https://www.taohuadao.org</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>官方论坛：https://bbs.taohuadao.net</strong><br /><strong>付费业务：https://www.taohuadao.net</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>官方github：https://github.com/TaohuadaoTech/CMS</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\">&nbsp;</p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><span style=\"color: #1f2328; font-family: -apple-system, \'system-ui\', \'Segoe UI\', \'Noto Sans\', Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\'; background-color: #ffffff;\">我们知道目前市面上有各种CMS系统，如苹果CMS、飞飞CMS以及传统的帝国CMS等等。他们都可以用来搭建影视网站，但是他们也都存在着各种问题，如系统臃肿、功能繁多，对于新手站长很不友好，代码分支很多、山寨埋有后门的版本在网络上流传，站长难以辨认。配置繁琐，如采集视频经常会遇到域名失效，需要操作数据库等，难以保证网站随时都能正常访问，一旦出现问题就会造成用户无法访问，用户流失等。所以我们才针对这些站长的痛点，推出了专属于影视建站系统领域的桃花岛CMS系统。</span></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\">&nbsp;</p>',
        ];
    }

    /**
     * 发送GET请求
     * @param $url
     * @param $params
     * @return mixed|string
     */
    private function get($url, $params = [])
    {
        $options = [
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 5
        ];
        return Http::get($url, $params, $options);
    }

    /**
     * 校验Response
     * @param $response
     * @return mixed
     */
    private function verifyResponse($response)
    {
        if (empty($response)) {
            throw new \RuntimeException('接口返回信息为空字符串');
        }
        $response_array = json_decode($response, true);
        if (isset($response_array['code']) && $response_array['code'] === 0) {
            if (isset($response_array['data'])) {
                return $response_array['data'];
            }
        }
        throw new \RuntimeException("接口返回信息格式错误: {$response}");
    }

    /**
     * 计算总页码
     * @param $total_count
     * @param $limit
     * @return float|int
     */
    private function getTotalPn($total_count, $limit)
    {
        return $total_count % $limit == 0 ? number_down($total_count / $limit, 0) : number_down($total_count / $limit, 0) + 1;
    }

}
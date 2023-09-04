<?php


namespace app\common\library;


use app\common\library\handle\ConfigurationHandle;
use app\common\model\WebModelDownload;
use app\common\model\WebSites;
use think\Cache;

class SiteUtil
{

    /**
     * 初始化站点数据
     * @param \think\db\Connection $instance
     * @param $mysql_prefix
     * @param $domain
     * @param $expire
     */
    public static function initSiteData(\think\db\Connection $instance, $mysql_prefix, $domain, $expire)
    {
        if (!defined('INSTALL_PATH')) {
            define('INSTALL_PATH', APP_PATH . 'admin' . DS . 'command' . DS . 'Install' . DS);
        }
        if (!IS_CLI) {
            // 创建默认站点
            try {
                $site = $instance->name('web_sites')->find();
                if (empty($site)) {
                    $template_where = [];
                    $templates_model = $instance->name('sync_templates');
                    $template_id = Cache::pull(ConfigurationHandle::getBasicCacheKeyDefaultTempate());
                    if (empty($template_id)) {
                        $template_where['name'] = ConfigurationHandle::getBasicSiteDefaultTemplateName();
                    } else {
                        $template_where['id'] = $template_id;
                    }
                    $template = $templates_model->where($template_where)->find();
                    if (empty($template)) {
                        // 模板不存在，先同步模板信息
                        StandApi::getInstance()->syncTemplate();
                        $template = $templates_model->where($template_where)->find();
                    }
                    if (!empty($template)) {
                        // 设置默认站点信息
                        $template_name = SiteUtil::downloadSiteModel($template['file']);
                        if (!empty($template_name)) {
                            $_id = $instance->name('web_model_download')->insertGetId([
                                'model_id' => $template['id'],
                                'model_name' => $template['name'],
                                'model_version' => $template['version'],
                                'model_path' => $template_name,
                                'model_theme' => $template['theme'],
                                'model_status' => WebModelDownload::STATUS_ENABLE,
                                'create_time' => time()
                            ]);
                            if ($_id) {
                                $instance->name('web_sites')->insert([
                                    'name' => '桃花岛CMS',
                                    'domain' => $domain,
                                    'describe' => '桃花岛CMS',
                                    'keyword' => '桃花岛CMS',
                                    'model_id' => $_id,
                                    'model_path' => $template_name,
                                    'model_theme' => $template['theme'],
                                    'declaration' => '欢迎使用桃花岛CMS系统，请访问官网www.taohuadeo.org',
                                    'status' => WebSites::STATUS_ENABLE,
                                    'create_time' => time()
                                ]);
                                // 站点创建完成后清除改站点缓存
                                Redis::getInstance()->del(Redis::REDIS_KEY_WEB_SITE_TEMPLATE_PATH . $domain);
                            }
                        }
                    }
                }
            } catch (\Throwable $ex) {
                Logger::error('SiteUtil::initSiteData => 下载默认模板异常: ' . $ex->getMessage());
                Logger::error($ex->getTraceAsString());
            }
        }
        // 执行片源SQL
        try {
            Cache::set(ConfigurationHandle::getBasicCacheKeyInitVideosInfo(), time(), $expire);
            $videos_sql = file_get_contents(INSTALL_PATH . 'videos.sql');
            $videos_sql = str_replace("`fa_", "`{$mysql_prefix}", $videos_sql);
            $instance->getPdo()->exec($videos_sql);
            Cache::rm(ConfigurationHandle::getBasicCacheKeyInitVideosInfo());
        } catch (\Throwable $ex) {
            Logger::error('SiteUtil::initSiteData => 执行片源SQL异常: ' . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
    }

    /**
     * 下载模板
     * @param $file
     * @return bool|mixed
     */
    public static function downloadSiteModel($file)
    {
        $suffix = substr($file, strrpos($file, '.'));
        $template_name = str_replace($suffix, '', substr($file, strrpos($file, '/') + 1));
        $template_download_path = ROOT_PATH . ConfigurationHandle::getBasicSiteTemplateDownloadPath();
        $template_download_zip_file = $template_download_path . DIRECTORY_SEPARATOR . $template_name . $suffix;
        try {
            $source = fopen($file, 'r');
            $destination = fopen($template_download_zip_file, 'w');
            stream_copy_to_stream($source, $destination);
        } catch (\Throwable $ex) {
            Logger::error('SiteUtil:downloadSiteModel => 下载失败: ' . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
            return false;
        } finally {
            isset($source) ? fclose($source) : null;
            isset($destination) ? fclose($destination) : null;
        }
        try {
            $zip = new \ZipArchive();
            if ($zip->open($template_download_zip_file) === TRUE) {
                if (!$zip->extractTo($template_download_path)) {
                    return false;
                }
            }
        } catch (\Throwable $ex) {
            Logger::error('SiteUtil:downloadSiteModel => 解压失败: ' . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
            return false;
        } finally {
            isset($zip) ? $zip->close() : null;
            unlink($template_download_zip_file);
        }
        return $template_name;
    }

}
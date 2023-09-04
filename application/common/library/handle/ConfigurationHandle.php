<?php

namespace app\common\library\handle;


class ConfigurationHandle
{

    // =========================================== 上传配置 ===========================================

    /**
     * 上传ICON图片时浏览器弹出的文件选择器中支持的文件类型
     * @return mixed
     */
    public static function getUploadIconMimetype()
    {
        return config('upload.icon_mimetype');
    }

    /**
     * 上传图片时浏览器弹出的文件选择器中支持的文件类型
     * @return mixed
     */
    public static function getUploadImageMimetype()
    {
        return config('upload.image_mimetype');
    }

    /**
     * 上传视频时浏览器弹出的文件选择器中支持的文件类型
     * @return mixed
     */
    public static function getUploadVideoMimetype()
    {
        return config('upload.video_mimetype');
    }

    // =========================================== 系统配置 ===========================================

    /**
     * 开启邮箱验证
     * @return mixed
     */
    public static function getSystemConfigMailSwitch()
    {
        return config('site.mail_switch');
    }

    /**
     * 开启防止Gmail多别名
     * @return mixed
     */
    public static function getSystemConfigPreventGmailAliasSwitch()
    {
        return config('site.prevent_gmail_alias_switch');
    }

    /**
     * 开启邮箱后缀白名单
     * @return mixed
     */
    public static function getSystemConfigMailSuffixWhitelistSwitch()
    {
        return config('site.mail_suffix_whitelist_switch');
    }

    /**
     * 邮箱后缀白名单
     * @return mixed
     */
    public static function getSystemConfigMailSuffixWhitelist()
    {
        return config('site.mail_suffix_whitelist');
    }

    /**
     * 开启防机器人
     * @return mixed
     */
    public static function getSystemConfigAntiMachineSwitch()
    {
        return config('site.anti_machine_switch');
    }

    /**
     * 密钥
     * @return mixed
     */
    public static function getSystemConfigGoogleRecaptchaKey()
    {
        return config('site.google_recaptcha_key');
    }

    /**
     * 网站密钥
     * @return mixed
     */
    public static function getSystemConfigGoogleRecaptchaSiteKey()
    {
        return config('site.google_recaptcha_site_key');
    }

    /**
     * 验证得分
     * @return mixed
     */
    public static function getSystemConfigGoogleRecaptchaScore()
    {
        return config('site.google_recaptcha_score');
    }

    /**
     * 开启IP注册限制
     * @return mixed
     */
    public static function getSystemConfigIpRegisteredRestriction()
    {
        return config('site.ip_registered_restriction');
    }

    /**
     * 开启IP注册限制
     * @return mixed
     */
    public static function getSystemConfigIpMaxRegisteredTimes()
    {
        return config('site.ip_max_registered_times');
    }

    /**
     * 惩罚时间
     * @return mixed
     */
    public static function getSystemConfigIpPunishmentTime()
    {
        return config('site.ip_punishment_time');
    }

    /**
     * 几秒后跳过广告
     * @return mixed
     */
    public static function getSystemConfigSkipAdsAfterSeconds()
    {
        return config('site.skip_ads_after_seconds');
    }

    // =========================================== 基础配置 ===========================================

    /**
     * 默认模板名称
     * @return mixed
     */
    public static function getBasicSiteDefaultTemplateName()
    {
        return config('basic.site_default_template_name');
    }

    /**
     * 站点模板下载目录
     * @return mixed
     */
    public static function getBasicSiteTemplateDownloadPath()
    {
        return config('basic.site_template_download_path');
    }

    /**
     * 模板基础路径
     * @return mixed
     */
    public static function getBasicTemplateBasicPath()
    {
        return config('basic.template_basic_path');
    }

    /**
     * 后台安全路径验证正则
     * @return mixed
     */
    public static function getBasicBackgroundPathReg()
    {
        return config('basic.background_path_reg');
    }

    /**
     * 安装时初始化片源缓存KEY
     * @return mixed
     */
    public static function getBasicCacheKeyInitVideosInfo()
    {
        return config('basic.cache_key_init_videos_info');
    }

    /**
     * 安装时使用到的临时缓存KEY1
     * @return mixed
     */
    public static function getBasicCacheKeyDefaultTempate()
    {
        return config('basic.cache_key_default_tempate');
    }

    /**
     * 安装时使用到的临时缓存KEY2
     * @return mixed
     */
    public static function getBasicCacheKeyMysqlConnectData()
    {
        return config('basic.cache_key_mysql_connect_data');
    }

    /**
     * reCAPTCHA 验证API地址
     * @return mixed
     */
    public static function getBasicRecaptchaVerifyApi()
    {
        return config('basic.recaptcha_verify_api');
    }

    // =========================================== 总站API配置 ===========================================

    /**
     * API HSOT
     * @return mixed
     */
    public static function getStandApiHost()
    {
        return config('stand.api_host');
    }

    /**
     * 获取公告信息
     * @return mixed
     */
    public static function getStandGetSystemNoticeUri()
    {
        return config('stand.get_system_notice_uri');
    }

    /**
     * 获取分类
     * @return mixed
     */
    public static function getStandGetCategoryUri()
    {
        return config('stand.get_category_uri');
    }

    /**
     * 获取视频
     * @return mixed
     */
    public static function getStandGetVideoUri()
    {
        return config('stand.get_video_uri');
    }

    /**
     * 获取模板
     * @return mixed
     */
    public static function getStandGetTemplateUri()
    {
        return config('stand.get_template_uri');
    }

    /**
     * 获取视频源
     * @return mixed
     */
    public static function getStandGetOriginUri()
    {
        return config('stand.get_origin_uri');
    }

    /**
     * 获取使用说明
     * @return mixed
     */
    public static function getStandGetInstructionUri()
    {
        return config('stand.get_instruction_uri');
    }

}
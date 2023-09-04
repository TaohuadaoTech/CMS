<?php

//上传配置
return [
    /**
     * 上传地址,默认是本地上传
     */
    'uploadurl' => 'ajax/upload',
    /**
     * CDN地址
     */
    'cdnurl'    => '',
    /**
     * 文件保存格式
     */
    'savekey'   => '/uploads/{year}{mon}{day}/{filemd5}{.suffix}',
    /**
     * 最大可上传大小
     */
    'maxsize'   => '50mb',
    /**
     * 可上传的文件类型
     */
    'mimetype'  => 'jpg,png,bmp,jpeg,gif,webp,ico,mp4',
    /**
     * 是否支持批量上传
     */
    'multiple'  => false,
    /**
     * 是否支持分片上传
     */
    'chunking'  => false,
    /**
     * 默认分片大小
     */
    'chunksize' => 2097152,
    /**
     * 完整URL模式
     */
    'fullmode' => false,
    /**
     * 缩略图样式
     */
    'thumbstyle' => '',
    /**
     * 上传ICON图片时浏览器弹出的文件选择器中支持的文件类型
     */
    'icon_mimetype' => 'image/x-icon,image/vnd.microsoft.icon',
    /**
     * 上传图片时浏览器弹出的文件选择器中支持的文件类型
     */
    'image_mimetype' => 'image/jpeg,image/png,image/jpg,image/bmp,image/gif,image/webp',
    /**
     * 上传视频时浏览器弹出的文件选择器中支持的文件类型
     */
    'video_mimetype' => 'video/mp4',
];

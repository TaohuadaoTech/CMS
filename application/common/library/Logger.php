<?php


namespace app\common\library;


class Logger
{

    /**
     * 日志记录
     * @param $msg
     * @param string $type
     * @param bool $force
     */
    public static function write($msg, $type = 'log', $force = false)
    {
        \think\Log::write($msg, $type, $force);
    }

    /**
     * 错误日志记录
     * @param string $msg
     * @param bool $force
     */
    public static function error($msg, $force = false) {
        \think\Log::write($msg, 'error', $force);
    }

}
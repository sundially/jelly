<?php

namespace app\components;

/**
 * 时间辅助函数
 *
 */
class SgdTime
{
    /**
     * 现在时间
     *
     * @return bool|string
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * es的时间
     */
    public static function esNow()
    {
        return date('Y-m-d\TH:i:s+08:00');
    }

    public static function day()
    {
        return date('Y-m-d');
    }

    /**
     * 判断时间是否为空
     *
     * @param $time
     * @return bool
     */
    public static function isZero($time)
    {
        return $time == static::zero();
    }

    /**
     * 空时间
     *
     * @return string
     */
    public static function zero()
    {
        return '0000-00-00 00:00:00';
    }

    /**
     * 转化成标准时间
     *
     * @param $time
     * @return bool|string
     */
    public static function norm($time)
    {
        return date('Y-m-d H:i:s', $time);
    }
}
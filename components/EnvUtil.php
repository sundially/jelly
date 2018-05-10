<?php

namespace app\components;

/**
 * 环境配置相关
 */
class EnvUtil
{

    /**
     * 获取当前环境
     *
     * @return bool
     */
    public static function getEvn()
    {
        return defined ('YII_ENV') ?  YII_ENV : 'Unknown';
    }

    /**
     * 是否为dev环境
     *
     * @return bool
     */
    public static function isDevEvn()
    {
        return defined ('YII_ENV') && (YII_ENV === 'dev');
    }


    /**
     * 是否为beta环境
     *
     * @return bool
     */
    public static function isBetaEvn()
    {
        return defined ('YII_ENV') && (YII_ENV === 'beta');
    }

    /**
     * 是否为prod环境
     *
     * @return bool
     */
    public static function isProdEvn()
    {
        return defined ('YII_ENV') && (YII_ENV === 'prod');
    }

    /**
     * 是否为本地环境
     */
    public static function isLocalEvn ()
    {
        return defined ('YII_ENV') && (YII_ENV == 'local');
    }

    /**
     * 是否为test环境
     */
    public static function isTestEvn ()
    {
        return defined ('YII_ENV') && (YII_ENV == 'test');
    }
}
<?php

namespace app\components;

/**
 * Redis Key 管理类
 * key值命名规范
 *      key类型缩写:业务前缀:自定义
 *
 * key类型缩写:
 *      k : key-value
 *      l : list
 *      s : set
 *      h : hash
 *      z : zset
 *
 * 业务前缀:
 *      自由定义,在分割线中申明
 *
 * 自定义:
 *      设计变量使用##包含
 *      如: #user_id#  #date#
 *      使用时替换 $key = str_replace('#userid#', $userId, RedisKeys::REDIS_KEY_****);
 *
 */
class RedisKeys
{
    /**
     * userId
     */
    const USER_ID = '#userid#';
    /**
     * day
     */
    const DAY = '#day#';
    /**
     * month
     */
    const MONTH = '#month#';

    /**
     * 用户登录失败次数
     */
    const PLATFORM_LOGIN_FAIL_COUNT = 'h:plat:login:fail:count:#day#';

}
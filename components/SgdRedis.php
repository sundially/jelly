<?php

namespace app\components;
use yii;
use yii\redis\Connection;

/**
 * Redis Helper Class
 *
 */
class SgdRedis
{
    /**
     * Execute a redis command
     *
     * @param \yii\redis\Connection $conn the redis connection
     * @param string $name the name of the command
     * @param array $params the params of the command
     * @return array|bool|null|string
     */
    public static function executeCommand(Connection $conn, $name, $params = [])
    {
        $ret = $conn->executeCommand($name, $params);

        $params = strtr(var_export($params, true), ["\t" => '', "\r" => '', "\n" => '', "    " => '']);
        if (is_array($ret)) {
            $result = strtr(var_export($ret, true), ["\t" => '', "\r" => '', "\n" => '', "    " => '']);
        } else {
            $result = $ret;
        }

        Yii::info('msg[redis-execute] command[' . $name .'] params[' . $params . '] ret[' . $result . ']', __METHOD__);
        return $ret;
    }

    /**
     * Execute command set
     *
     * @param $key
     * @param $value
     * @param int $expire
     * @return array|bool|null|string
     */
    public static function executeSet($key, $value, $expire = 0)
    {
        $redis = static::getRedis();
        $ret = static::executeCommand($redis, 'SET', [$key, $value]);
        if ($expire != 0) {
            static::executeCommand($redis, 'EXPIRE', [$key, $expire]);
        }

        return $ret;
    }

    /**
     * Execute command get
     *
     * @param $key
     * @return array|bool|null|string
     */
    public static function executeGet($key)
    {
        $redis = static::getRedis();
        return static::executeCommand($redis, 'GET', [$key]);
    }

    /**
     * Get a value of a key which is associated with user
     *
     * @param string $userId the user id
     * @param string $preDefinedKey the pre defined key
     * @return array|bool|null|string
     */
    public static function get($userId, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getKey($userId, $preDefinedKey);
        return static::executeCommand($redis, 'GET', [$key]);
    }

    /**
     * Set a value of a key which is associated with user
     *
     * @param string $userId the user id
     * @param string $value the value of the key
     * @param string $preDefinedKey the pre defined key
     * @param int $expire the expire time of the key in seconds format
     * @return array|bool|null|string
     */
    public static function set($userId, $value, $preDefinedKey, $expire=0)
    {
        $redis =  static::getRedis();
        $key = static::getKey($userId, $preDefinedKey);
        $ret =  static::executeCommand($redis, 'SET', [$key, $value]);
        if ($expire != 0) {
            static::executeCommand($redis, 'EXPIRE', [$key, $expire]);
        }
        return $ret;
    }

    /**
     * Delete a key which is associated with user
     *
     * @param string $userId the id
     * @param string $preDefinedKey the pre defined key
     * @return array|bool|null|string
     */
    public static function del($userId, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getKey($userId, $preDefinedKey);
        return static::executeCommand($redis, 'DEL', [$key]);
    }

    /**
     * INCR a key which is associated with user
     *
     * @param string $userId the id
     * @param string $preDefinedKey the pre defined key
     * @return array|bool|null|string
     */
    public static function incr($userId, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getKey($userId, $preDefinedKey);
        return static::executeCommand($redis, 'INCR', [$key]);
    }

    /**
     * INCRBY a key
     *
     * @param $userId
     * @param $value
     * @param $preDefinedKey
     * @return array|bool|null|string
     */
    public static function incrBy($userId, $value, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getKey($userId, $preDefinedKey);
        return static::executeCommand($redis, 'INCRBYFLOAT', [$key, $value]);
    }

    /**
     * Increase the day value
     *
     * @param $userId
     * @param $value
     * @param $preDefinedKey
     * @return array|bool|null|string
     */
    public static function dayIncr($userId, $value, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getDayKey($preDefinedKey);
        $ret = static::executeCommand($redis, 'HINCRBYFLOAT', [$key, $userId, $value]);
        static::executeCommand($redis, 'EXPIRE', [$key, 24*3600]);
        return $ret;
    }

    /**
     * Get the day value
     *
     * @param $userId
     * @param $preDefinedKey
     * @return array|bool|null|string
     */
    public static function daySet($userId, $value, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getDayKey($preDefinedKey);
        $ret = static::executeCommand($redis, 'HSET', [$key, $userId, $value]);
        static::executeCommand($redis, 'EXPIRE', [$key, 24*3600]);
        return $ret;
    }

    /**
     * Get the day value
     *
     * @param $userId
     * @param $preDefinedKey
     * @return array|bool|null|string
     */
    public static function dayGet($userId, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getDayKey($preDefinedKey);
        return static::executeCommand($redis, 'HGET', [$key, $userId]);
    }

    /**
     * Increase the day value
     *
     * @param $userId
     * @param $value
     * @param $preDefinedKey
     * @return array|bool|null|string
     */
    public static function monthIncr($userId, $value, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getMonthKey($preDefinedKey);
        $ret = static::executeCommand($redis, 'HINCRBYFLOAT', [$key, $userId, $value]);
        static::executeCommand($redis, 'EXPIRE', [$key, 32*24*3600]);
        return $ret;
    }

    /**
     * Get the day value
     *
     * @param $userId
     * @param $preDefinedKey
     * @return array|bool|null|string
     */
    public static function monthSet($userId, $value, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getMonthKey($preDefinedKey);
        $ret = static::executeCommand($redis, 'HSET', [$key, $userId, $value]);
        static::executeCommand($redis, 'EXPIRE', [$key, 24*3600]);
        return $ret;
    }

    /**
     * Get the day value
     *
     * @param $userId
     * @param $preDefinedKey
     * @return array|bool|null|string
     */
    public static function monthGet($userId, $preDefinedKey)
    {
        $redis =  static::getRedis();
        $key = static::getMonthKey($preDefinedKey);
        return static::executeCommand($redis, 'HGET', [$key, $userId]);
    }


    /**
     * Get the Connection of redis
     *
     * @return Connection
     */
    public static function getRedis()
    {
        return Yii::$app->riskRedis;
    }

    /**
     * Get the key of a specific user
     *
     * @param string $userId the id
     * @param string $preDefinedPrefix the predifined key
     * @return string
     */
    public static function getKey($userId, $preDefinedPrefix)
    {
        return str_replace(RedisKeys::USER_ID, $userId, $preDefinedPrefix);
    }

    /**
     * Get the day key of a specific user
     *
     * @param $preDefinedPrefix
     * @return mixed
     */
    public static function getDayKey($preDefinedPrefix)
    {
        return str_replace(RedisKeys::DAY, date('Ymd'), $preDefinedPrefix);
    }

    /**
     * Get the month key of a specific user
     *
     * @param $preDefinedPrefix
     * @return mixed
     */
    public static function getMonthKey($preDefinedPrefix)
    {
        return str_replace(RedisKeys::MONTH, date('Ym'), $preDefinedPrefix);
    }
}
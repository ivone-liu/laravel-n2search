<?php
/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/27
 * Time: 16:08
 */

namespace N2Search\Core;

class N2Tools
{
    /**
     * Desc: 获取一个redis的长连接
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 16:33
     * @return \Redis
     */
    public static function getRedis() {
        $redis = new \Redis();
        $redis->pconnect(self::getConfig('redis_host'), self::getConfig('port'));
        $redis->auth(self::getConfig('password'));
        $redis->select(self::getConfig('db'));
        return $redis;
    }

    /**
     * Desc: 读取配置
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 14:03
     * @param $key
     * @return mixed
     */
    public static function getConfig($key) {
        $config = config("N2Search.{$key}");
        return $config;
    }

}
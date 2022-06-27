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
        $redis->pconnect(env('REDIS_HOST', '127.0.0.1'), env('REDIS_PORT', '6379'));
        $redis->auth(env('REDIS_PASSWORD', '6379'));
        return $redis;
    }

}
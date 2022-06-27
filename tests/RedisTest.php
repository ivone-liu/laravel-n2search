<?php

namespace N2Search\Tests;

/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/27
 * Time: 16:12
 */
class RedisTest
{

    public function test() {
        $redis = new \Redis();
        $redis->pconnect('127.0.0.1', 6379);
        $id = $redis->getPersistentID();
        echo $id;
    }

}

$obj = new RedisTest();
$obj->test();
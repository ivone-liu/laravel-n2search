<?php
/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/29
 * Time: 14:27
 */

namespace N2Search;

use N2Search\Core\Find;
use N2Search\Core\Load;

class N2Search
{

    protected $redis;
    protected $stop_words = [];

    public function __construct($config = '') {
        if (empty($config)) {
            $config = config("N2Search");
        }
        $this->redis = [
            'host'              =>  $config['redis_host'],
            'port'              =>  $config['redis_port'],
            'password'          =>  $config['redis_password'],
            'db'                =>  $config['redis_db']
        ];

        $this->stop_words = $config['stop_words'];
        $this->dict = $config['dict'];
    }

    public function load($model, $columns): Load {
        return new Load($model, $columns, $this);
    }

    public function find($model, $key): Find {
        return new Find($model, $key, $this);
    }

    public function getN2Config() {
        return ['redis'=>$this->redis, 'stop_words'=>$this->stop_words, 'dict'=>$this->dict];
    }

    public function redisConnect(): \Redis {
        $redis = new \Redis();
        $redis->pconnect($this->redis['host'], $this->redis['port']);
        $redis->auth($this->redis['password']);
        $redis->select($this->redis['db']);
        return $redis;
    }

}
<?php
/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/29
 * Time: 14:27
 */

namespace N2Search;

class N2Search
{

    protected $redis;
    protected $stop_words = [];

    public function __construct($config) {
        $this->redis = [
            'host'              =>  $config['redis_host'],
            'port'              =>  $config['redis_port'],
            'redis_password'    =>  $config['redis_password'],
            'db'                =>  $config['redis_db']
        ];

        $this->stop_words = $config['stop_words'];
        $this->dict = $config['dict'];
    }

    public function load($model, $columns): Load {
        return new Load($model, $columns, $this);
    }

    public function find($model, $key, $columns = ['*'], $order = 'desc', $order_columns = ['id'], $page = 1, $size = 20): Find {
        return new Find($model, $key, $columns = ['*'], $order = 'desc', $order_columns = ['id'], $page = 1, $size = 20, $this);
    }
    
}
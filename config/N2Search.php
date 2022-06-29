<?php

return [
    'redis_host'        => env('REDIS_HOST', '127.0.0.1'),
    'redis_password'    => env('REDIS_PASSWORD', null),
    'redis_port'        => env('REDIS_PORT', '6379'),
    'redis_db'          =>  9,

    'dict'              => 'big',

    'job_work'          => 0,

    'stop_words'        =>  [
        '我',
        '的',
        '你',
        '他',
        '是',
        '啊',
        '呀',
        '。',
        '，',
        '：',
        '！',
        '？',
        '哈',
        '不',
        '了',
        '、',
        '也',
        '啥',
        '把'
    ]
];
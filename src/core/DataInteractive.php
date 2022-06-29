<?php

namespace N2Search\Core;

use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;

/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/27
 * Time: 15:54
 */
class DataInteractive
{

    /**
     * Desc: 分词
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 15:59
     * @param $content
     * @param $dict 默认用最大词典，完全切割
     * @param $html_strap 默认认为content是html标记
     */
    public static function cut($content, $dict = '', $html_strap = 1) {
        Jieba::init([
            'dict'      =>  !empty($dict) ? $dict : N2Tools::getConfig('dict'),
            'cjk'       =>  'all'
        ]);
        Finalseg::init();

        if ($html_strap) {
            $content = strip_tags($content);
        }
        $cut = Jieba::cutForSearch($content);
        return $cut;
    }

    /**
     * Desc: 数据更新
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 16:41
     * @param $key
     * @param $data
     */
    public static function add($key, $data) {
        $redis = N2Tools::getRedis();
        $redis->set($key, $data);
    }

    /**
     * Desc: 数据读取
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 16:41
     * @param $key
     * @return false|mixed|\Redis|string
     */
    public static function read($key) {
        $redis = N2Tools::getRedis();
        $kv = $redis->get($key);
        return $kv;
    }

    public static function delete() {}


}
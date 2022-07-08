<?php
/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/7/8
 * Time: 10:38
 */

namespace N2Search;

use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\JiebaAnalyse;

class JiebaTokenizer
{
    public function __construct()
    {
        $config = config('jieba');

        Jieba::init([
            'dict'      =>  $config['dict'],
            'cjk'       =>  'all'
        ]);

        Finalseg::init();
        JiebaAnalyse::init();
    }

    public function getTokens($text) {
        return Jieba::cut($text);
    }

    public function deriveTokens($text) {
        return Jieba::cutForSearch($text);
    }

    public function analysis($text) {
        return JiebaAnalyse::extractTags($text);
    }
}
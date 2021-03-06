<?php

namespace N2Search\Core;

use N2Search\Jobs\ImportJob;
use N2Search\N2Search;
use Overtrue\Pinyin\Pinyin;

/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/27
 * Time: 15:50
 */
class Load
{

    protected $n2;
    protected $db;
    protected $columns;
    protected $n2_config;
    protected $db_class;
    protected $model_primary_key;

    public function __construct($db_class, array $columns, N2Search $n2) {
        $this->db_class = $db_class;
        $this->db = new $this->db_class;
        $this->n2 = $n2;
        $this->columns = $columns;
        $this->n2_config = $n2->getN2Config();
        $this->model_primary_key = $this->db->getKeyName();
    }

    /**
     * Desc: 增加一条
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 16:38
     * @param $model
     * @param $columns ID必选
     *
     * add_one(NotesModel::class, ['id', 'note'])
     * key:{1,2,3,4,5}
     *
     */
    public function addOne(int $id, $need_queue = 0) {
        if ($need_queue == 1) {
            ImportJob::dispatch($this->n2, $this->db_class, $this->columns, $id, 0)->onQueue("n2_build");
            return;
        }
        $log = $this->db->where([$this->model_primary_key=>$id])->first();
        if (empty($log)) {
            return;
        }
        $log = $log->toArray();
        foreach ($this->columns as $item) {
            $this->curForWords($log, $item);
        }
    }

    /**
     * Desc: 批量增加
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 16:43
     * @param $model
     * @param $columns
     */
    public function addBatch($need_queue = 0) {
        $base = [$this->model_primary_key];
        $this->columns = array_unique(array_merge($base, $this->columns));

        $page = 1;
        $size = 1000;

        while(1) {
            $log = $this->db->skip(($page-1)*$size)->take($size)->get();
            if ($log->isEmpty()) {
                break;
            }
            $log = $log->toArray();

            if ($need_queue == 1) {
                ImportJob::dispatch($this->n2, $this->db_class, $this->columns, 0, 1, $log)->onQueue("n2_build");
                continue;
            }

            foreach ($log as $item) {
                foreach ($this->columns as $column) {
                    $this->curForWords($item, $column);
                }
            }
            $page += 1;
        }
    }

    /**
     * Desc: 分词
     * Author: Ivone <i@ivone.me>
     * Date: 2022/7/7
     * Time: 16:56
     * @param $obj
     * @param $column
     */
    protected function curForWords($obj, $column) {
        if (!array_key_exists($column, $obj)) {
            return;
        }
        $sentence = $obj[$column];
        $sentence = strip_tags($sentence);
        $words = $this->n2->jieba->getTokens($sentence);
        foreach ($words as $word) {
            if (!empty($this->n2_config['pinyin'])) {
                $pinyin_cut = $this->pinyin(mb_strtolower($word));
                DataInteractive::add(implode('', $pinyin_cut), $word, $this->n2);
            }
            $this->save($word, $obj);
        }
        $analysis = $this->n2->jieba->analysis($sentence);
        DataInteractive::add($obj[$this->model_primary_key]."_".$column."_ans", json_encode($analysis, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $this->n2);
    }

    /**
     * Desc: 存储倒排关系
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 16:43
     * @param $word
     * @param $db_muster
     */
    protected function save($word, $db_muster) {
        if (in_array($word, $this->n2_config["stop_words"])) {
            return;
        }
        DataInteractive::add($word, $db_muster[$this->model_primary_key], $this->n2);
    }

    /**
     * Desc: 转拼音
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/30
     * Time: 9:14
     * @param $word
     */
    protected function pinyin($word) {
        $pinyin = new Pinyin();
        $cut = $pinyin->convert($word);
        return $cut;
    }

    /**
     * Desc: 分词后的BXN
     * Author: Ivone <i@ivone.me>
     * Date: 2022/7/7
     * Time: 15:00
     * @param $word
     */
    protected function bxnForKey($word) {

    }

}
<?php

namespace N2Search\Core;

use Illuminate\Database\Eloquent\Builder;
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

    public function __construct(Builder $db_model, array $columns, N2Search $n2) {
        $this->db = $db_model;
        $this->n2 = $n2;
        $this->columns = $columns;
        $this->n2_config = $n2->getN2Config();
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
    public function addOne(int $id, $need_pinyin = 0, $need_queue = 0) {
        if ($need_queue == 1) {
            ImportJob::dispatch($this->db, $this->columns, $id, $need_pinyin, 0)->onQueue("n2_build");
            return;
        }
        $log = $this->db->where(['id'=>$id])->first()->toArray();
        foreach ($this->columns as $item) {
            if (!array_key_exists($item, $log)) {
                continue;
            }
            $words = DataInteractive::cut($log[$item], $this->n2->dict);
            foreach ($words as $word) {
                if (!empty($need_pinyin)) {
                    $pinyin_cut = $this->pinyin($word);
                    foreach ($pinyin_cut as $pinyin) {
                        $this->save($pinyin, $log);
                    }
                }
                $this->save($word, $log);
            }
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
    public function addBatch($need_pinyin = 0, $need_queue = 0) {
        $base = ['id'];
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
                ImportJob::dispatch($this->db, $this->columns, 0, $need_pinyin, 1, $log)->onQueue("n2_build");
                continue;
            }

            foreach ($log as $item) {
                foreach ($this->columns as $column) {
                    if (!array_key_exists($column, $item)) {
                        continue;
                    }

                    $words = DataInteractive::cut($item[$column]);
                    foreach ($words as $word) {
                        if (!empty($need_pinyin)) {
                            $pinyin_cut = $this->pinyin($word);
                            foreach ($pinyin_cut as $pinyin) {
                                $this->save($pinyin, $log);
                            }
                        }
                        $this->save($word, $item);
                    }
                }
            }
            $page += 1;
        }
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
        $cut_data = DataInteractive::read($word, $this->n2);
        if (empty($cut_data)) {
            $ids = [$db_muster['id']];
        } else {
            $ids = json_decode($cut_data, true);
            array_push($ids, $db_muster['id']);
            $ids = array_unique($ids);
        }
        DataInteractive::add($word, json_encode($ids), $this->n2);
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

}
<?php

namespace N2Search\Core;

use N2Search\Core\DataInteractive;
use Illuminate\Database\Eloquent\Builder;
use N2Search\N2Search;

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
    public function add_one() {
        $log = $this->db->first()->toArray();
        foreach ($this->columns as $item) {
            if (!array_key_exists($item, $log)) {
                continue;
            }
            $words = DataInteractive::cut($log[$item], $this->n2->dict);
            foreach ($words as $word) {
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
    public function add_batch() {
        $base = ['id'];
        $columns = array_unique(array_merge($base, $this->columns));

        $log = $this->db->get();
        $log = $log->isEmpty() ? [] : $log->toArray();
        if (empty($log)) {
            return;
        }

        foreach ($log as $item) {
            foreach ($columns as $column) {
                if (!array_key_exists($column, $item)) {
                    continue;
                }

                $words = DataInteractive::cut($item[$column]);
                foreach ($words as $word) {
                    $this->save($word, $item);
                }
            }
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
        if (in_array($this->n2_config["stop_words"], $word)) {
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

}
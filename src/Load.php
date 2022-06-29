<?php

namespace N2Search;

use N2Search\Core\DataInteractive;

/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/27
 * Time: 15:50
 */
class Load
{
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
    public function add_one($model, $columns = ['*']) {
        $log = $model->first()->toArray();
        foreach ($columns as $item) {
            if (!array_key_exists($item, $log)) {
                continue;
            }
            $words = DataInteractive::cut($log[$item]);
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
    public function add_batch($model, $columns = ['*']) {
        $base = ['id'];
        $columns = array_unique(array_merge($base, $columns));

        $log = $model->get();
        $log = $log->isEmpty() ? [] : $log->toArray();
        if (empty($log)) {
            return;
        }

        foreach ($log as $item) {
            foreach ($columns as $column) {
                $words = DataInteractive::cut($item[$column]);
                foreach ($words as $word) {
                    $this->save($word, $log);
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
        $cut_data = DataInteractive::read($word);
        if (empty($cut_data)) {
            $ids = [$db_muster['id']];
        } else {
            $ids = json_decode($cut_data, true);
            array_push($ids, $db_muster['id']);
            $ids = array_unique($ids);
        }
        DataInteractive::add($word, json_encode($ids));
    }

}
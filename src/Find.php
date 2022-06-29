<?php

namespace N2Search;

use N2Search\Core\N2Tools;

/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/27
 * Time: 15:51
 */
class Find
{

    /**
     * Desc: 读取
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 16:50
     * @param $model
     * @param $key
     * @param $select_columns
     * @param $order
     * @param $order_columns
     * @param $page
     * @param $size
     * @return array|mixed
     */
    public function muster($model, $key, $select_columns = ["*"], $order = 'desc', $order_columns = ['id'], $page = 1 , $size = 20) {
        $redis = N2Tools::getRedis();
        $kv = $redis->get($key);
        $ids = json_decode($kv, true);

        $query = $model->select($select_columns)->whereIn('id', $ids);
        foreach ($order_columns as $column) {
            $query = $query->orderBy($column, $order);
        }
        $cluster = $query->skip(($page-1)*$size)->get();
        $cluster = $cluster->isEmpty() ? [] : $cluster->toArray();
        return $cluster;
    }

}
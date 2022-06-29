<?php

namespace N2Search;

use N2Search\Core\N2Tools;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/27
 * Time: 15:51
 */
class Find
{

    protected $db;
    protected $key;
    protected $select;
    protected $order;
    protected $order_columns;
    protected $page;
    protected $size;
    protected $n2;

    public function __construct(Model $model, String $key, Array $columns, String $order, Array $order_columns, Integer $page, Integer $size, N2Search $n2) {
        $this->db = $model;
        $this->key = $key;
        $this->select = $columns;
        $this->order = $order;
        $this->order_columns = $order_columns;
        $this->page = $page;
        $this->size = $size;
        $this->n2 = $n2;
    }

    /**
     * Desc: 读取
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 16:50
     * @return array|mixed
     */
    public function muster() {
        $redis = N2Tools::getRedis();
        $kv = $redis->get($this->key);
        $ids = json_decode($kv, true);

        $query = $this->db->select($this->select)->whereIn('id', $ids);
        foreach ($this->order_columns as $column) {
            $query = $query->orderBy($column, $this->order);
        }
        $cluster = $query->skip(($this->page-1)*$this->size)->get();
        $cluster = $cluster->isEmpty() ? [] : $cluster->toArray();
        return $cluster;
    }

}
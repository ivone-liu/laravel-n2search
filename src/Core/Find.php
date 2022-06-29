<?php

namespace N2Search\Core;

use Illuminate\Database\Eloquent\Builder;
use N2Search\N2Search;
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
    protected $n2;

    protected $query;

    public function __construct(Builder $model, string $key, N2Search $n2) {
        $this->db = $model;
        $this->key = $key;
        $this->n2 = $n2;

        $this->query = $this->db;
        $this->initQuery();
    }

    protected function initQuery() {
        $redis = $this->n2->redisConnect();
        $kv = $redis->get($this->key);
        $ids = json_decode($kv, true);

        $this->query = $this->query->select($this->select)->whereIn('id', $ids);
    }

    public function columns(array $select) {
        $this->query = $this->query->select($select);
    }

    public function page(int $page, int $size) {
        $this->query .= $this->query->skip(($this->page-1)*$this->size)->take($this->size);
    }

    public function where(array $where) {
        if(count($where) == count($where, 1)){
            $where = [$where];
        }
        foreach ($where as $item) {
            $this->query = $this->query->where($item);
        }
    }

    public function order($column, string $method = 'desc') {
        if (is_string($column)) {
            $column = [$column];
        }
        foreach ($column as $item) {
            $this->query = $this->query->orderBy($item, $this->order);
        }
    }

    /**
     * Desc: 读取
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/27
     * Time: 16:50
     * @return array|mixed
     */
    public function fetchMany() {
        $cluster = $this->query->get();
        $cluster = $cluster->isEmpty() ? [] : $cluster->toArray();
        return $cluster;
    }

    public function fetchOne() {
        $cluster = $this->query->first();
        return $cluster;
    }

}
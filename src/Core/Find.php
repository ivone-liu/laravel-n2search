<?php

namespace N2Search\Core;

use Illuminate\Database\Eloquent\Builder;
use N2Search\N2Search;

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
        // 初始化查询语句
        $this->initQuery();
    }

    /**
     * Desc: 初始化查询
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 16:44
     */
    protected function initQuery() {
        $ids = DataInteractive::read($this->key, $this->n2);
        if (!empty($ids)) {
            $this->query = $this->db->whereIn('id', $ids);
        }
    }

    /**
     * Desc: 字段查询
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 16:44
     * @param array $select
     */
    public function columns(array $select) {
        $this->query = $this->query->select($select);
        return $this;
    }

    /**
     * Desc: 分页查询
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 16:44
     * @param int $page
     * @param int $size
     */
    public function page(int $page, int $size) {
        $this->query = $this->query->skip(($page-1)*$size)->take($size);
        return $this;
    }

    /**
     * Desc: 条件查询
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 16:44
     * @param array $where
     */
    public function where(array $where) {
        if(count($where) == count($where, 1)){
            $where = [$where];
        }
        foreach ($where as $item) {
            $this->query = $this->query->where($item);
        }
        return $this;
    }

    /**
     * Desc: 排序查询
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 16:44
     * @param $column
     * @param string $method
     */
    public function order($column, string $method = 'desc') {
        if (is_string($column)) {
            $column = [$column];
        }
        foreach ($column as $item) {
            $this->query = $this->query->orderBy($item, $method);
        }
        return $this;
    }

    /**
     * Desc: 读取多条
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

    /**
     * Desc: 读取一条
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 16:44
     * @return mixed
     */
    public function fetchOne() {
        $cluster = $this->query->first();
        return $cluster;
    }

}
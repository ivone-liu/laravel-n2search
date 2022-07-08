<?php

namespace N2Search\Core;

use Fukuball\Jieba\Jieba;
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

    protected $class;
    protected $search_key;
    protected $keys;
    protected $n2;
    protected $db;
    protected $select;
    protected $cluster;
    protected $model_primary_key;

    protected $query;

    public function __construct($model, string $key, N2Search $n2) {
        $this->class = $model;
        $this->search_key = mb_strtolower(str_replace(' ', '', trim($key)));
        $this->db = new $this->class;
        $this->n2 = $n2;

        $this->model_primary_key = $this->db->getKeyName();

        // 初始化关键词
        $this->initKeys();
        // 初始化查询语句
        $this->initQuery();
    }

    protected function initKeys() {
        $keys = $this->n2->jieba->getTokens($this->search_key);
        $this->keys = $keys;
    }

    /**
     * Desc: 初始化查询
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 16:44
     */
    protected function initQuery() {
        $ids = [];
        foreach ($this->keys as $key) {
            $key_rel = DataInteractive::read($key, $this->n2);
            array_push($ids, $key_rel);
        }
        $ids = array_filter($ids);
        $ids = array_unique($ids);
        $this->query = $this->db->whereIn($this->model_primary_key, $ids[0]);
    }

    /**
     * Desc: 字段查询
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 16:44
     * @param array $select
     */
    public function columns(array $select) {
        $base = [$this->model_primary_key];
        $select = array_merge($base, $select);
        $this->select = $select;
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
        $this->cluster = $this->query->get();
        return $this;
    }

    /**
     * Desc: 读取
     * Author: Ivone <i@ivone.me>
     * Date: 2022/7/8
     * Time: 8:55
     * @return array
     */
    public function data() {
        return $this->cluster->isEmpty() ? [] : $this->cluster->toArray();
    }

    /**
     * Desc: 读取一条
     * Author: Ivone <i@ivone.me>
     * Date: 2022/6/29
     * Time: 16:44
     * @return mixed
     */
    public function fetchOne() {
        $this->cluster = $this->query->first();
        return $this->cluster;
    }

    /**
     * Desc: 分析排序
     * Author: Ivone <i@ivone.me>
     * Date: 2022/7/7
     * Time: 15:27
     * @param $docs
     * @return mixed|void
     */
    public function analysis() {
        $cluster = $this->cluster = $this->cluster->isEmpty() ? [] : $this->cluster->toArray();
        if (empty($this->select)) {
            return $cluster;
        }

        foreach ($cluster as $key=>$doc) {
            if (!array_key_exists('n2_weight', $cluster[$key])) {
                $cluster[$key]['n2_weight'] = 0.0;
            }
            foreach ($this->select as $item) {
                $analysis = DataInteractive::read($doc[$this->model_primary_key]."_".$item."_ans", $this->n2);
                if (empty($analysis)) {
                    continue;
                }
                $doc_analysis = json_decode($analysis[0], true);
                foreach ($doc_analysis as $k=>$ana) {
                    if (in_array($k, $this->keys)) {
                        $cluster[$key]['n2_weight'] += $ana;
                    }
                }

            }
        }
        return $cluster;
    }

}
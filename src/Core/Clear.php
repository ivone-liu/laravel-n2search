<?php
/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/30
 * Time: 9:41
 */

namespace N2Search\Core;

use N2Search\N2Search;

class Clear
{

    protected $n2;

    public function __construct(N2Search $n2) {
        $this->n2 = $n2;
    }

    public function flush() {
        $this->n2->redisConnect()->flushDB();
        return 1;
    }

    public function remove(...$keys) {
        foreach ($keys as $key) {
            $this->n2->redisConnect()->del($key);
        }
        return 1;
    }

}
<?php

namespace N2Search\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use N2Search\N2Search;

/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/29
 * Time: 16:13
 */
class ImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $n2;
    protected $pk;
    protected $need_iteration;
    protected $iteration_obj;

    public function __construct($n2, $pk, $need_iteration, $iteration_obj = [])
    {
        $this->n2 = $n2;
        $this->pk = $pk;
        $this->need_iteration = $need_iteration;
        $this->iteration_obj = $iteration_obj;
    }

    public function handle()
    {
        if (!empty($this->need_iteration)) {
            foreach ($this->iteration_obj as $item) {
                $this->add($item['id']);
            }
        } else {
            $this->add($this->pk);
        }
    }

    protected function add($pk) {
        $this->n2->addOne($pk);
    }

}
<?php

namespace N2Search\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
    protected $model;
    protected $columns;

    public function __construct($n2, $model, $columns)
    {
        $this->n2 = $n2;
        $this->model = $model;
        $this->columns = $columns;
    }

    public function handle()
    {
        
    }


}
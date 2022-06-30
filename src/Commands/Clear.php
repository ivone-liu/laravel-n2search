<?php
/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/30
 * Time: 10:08
 */

namespace N2Search\Commands;

use Illuminate\Console\Command;
use N2Search\N2Search;


class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'n2search:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理n2搜索的索引';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $n2 = new N2Search();
        $n2->clear()->flush();
    }
}

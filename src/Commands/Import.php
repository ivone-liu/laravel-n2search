<?php

namespace N2Search\Commands;

use Illuminate\Console\Command;
use N2Search\N2Search;


class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'n2search:build {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成n2搜索的索引';

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
        echo date("Y-m-d H:i:s", time()) . " start importing \n";

        $class = $this->argument('model');
        $model = new $class;

        $count = $model::count();
        $bar = $this->output->createProgressBar($count);
        $bar->setEmptyBarCharacter(' ');
        $bar->setProgressCharacter('>');
        $bar->setBarCharacter('<comment>=</comment>');

        $n2 = new N2Search();

        $page = 1;
        $size = 1000;
        while(1) {
            $logs = $model::skip(($page-1)*$size)->take($size)->get();
            if ($logs->isEmpty()) {
                break;
            }
            $logs = $logs->toArray();
            foreach ($logs as $log) {
                $n2->load($model::query(), ['note'])->addOne($log['id']);
                $bar->advance();
            }
            $page += 1;
        }

        $bar->finish();
    }
}

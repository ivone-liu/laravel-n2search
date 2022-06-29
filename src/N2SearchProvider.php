<?php
/**
 * Desc:
 * Author: Ivone <i@ivone.me>
 * Date: 2022/6/29
 * Time: 14:18
 */

namespace N2Search;

use Illuminate\Support\ServiceProvider;

class N2SearchProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__.'/config/N2Search.php' => config_path('n2search.php'),
        ]);
    }
}
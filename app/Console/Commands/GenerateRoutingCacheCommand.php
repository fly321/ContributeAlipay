<?php

namespace App\Console\Commands;

use App\Annotation\RequestMapping;
use App\Scan\Scanning as ScanningAlias;
use Illuminate\Console\Command;

class GenerateRoutingCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-routing-cache-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 扫描注解路由
        ScanningAlias::getInstance()->scan();
        $routes = [];
        // 生成路由
        foreach (ScanningAlias::$annotation['_m'] as $key => $value) {
            if ($key === RequestMapping::class) {
                foreach ($value as $k1 => $item) {
                    foreach ($item as $k2 => $v) {
                        $a = $v->getArguments();
                        $a["class"] = $k1;
                        // 获取controller
                        if (isset(ScanningAlias::$annotation['_c'][$key][$k1])) {
                            $o = ScanningAlias::$annotation['_c'][$key][$k1]->getArguments();
                            if (isset($o['prefix']) && $o['prefix'] !== "") {
                                $a["prefix"] = $o['prefix'];
                            } else {
                                // 类名转换为小写 _
                                $a["prefix"] = str_replace("\\", ".", strtolower($k1));
                            }
                        } else {
                            $a["prefix"] = str_replace("\\", ".", strtolower($k1));
                        }
                        $a["methods"] = $k2;
                        $routes[] = $a;
                    }
                }
            }
        }
        file_put_contents(base_path("routes.cache"), json_encode($routes));
    }
}

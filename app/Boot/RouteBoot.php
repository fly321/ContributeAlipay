<?php

namespace App\Boot;

use App\Annotation\RequestMapping;
use App\Scan\Scanning as ScanningAlias;
use Illuminate\Support\Facades\Route;

class RouteBoot
{
    /*public static function handle(){

        try {
            foreach (ScanningAlias::$annotation['_m'] as $key => $item) {
                if ($key === RequestMapping::class) {
                    foreach ($item as $k1 => $v1) {
                        foreach ($v1 as $k2 => $v3) {
                            $a = $v3->getArguments();
                            // 反射创建路由
                            $r = Route::match($a['method'], $a['path'], [$k1, $k2]);
                            // 判断当前类是否存在
                            if (isset(ScanningAlias::$annotation['_c'][$key][$k1])) {
                                $o = ScanningAlias::$annotation['_c'][$key][$k1]->getArguments();
                                if (isset($o['prefix']) && $o['prefix'] !== "") {
                                    $r->prefix($o['prefix']);
                                } else {
                                    // 类名转换为小写 _
                                    $r->prefix(str_replace("\\", ".", strtolower($k1)));
                                }

                            }

                            if (isset($a['middleware']) && $a['middleware'] !== "") {
                                $r->middleware($a['middleware']);
                            }

                            if (isset($a['domain']) && $a['domain'] !== "") {
                                $r->domain($a['domain']);
                            }

                            if (isset($a['name']) && $a['name'] !== "") {
                                $r->name($a['name']);
                            }

                        }
                    }
                }
            }
        } catch (\Exception $e) {
            ScanningAlias::getInstance()->scan();
            self::handle();
        }
    }*/

    public static function handle () {
        if (file_exists(base_path("routes.cache"))) {
            $routes = json_decode(file_get_contents(base_path("routes.cache")), true);
            foreach ($routes as $route) {
                $r = Route::match($route['method'], $route['path'], [$route['class'], $route['methods']]);
                if (isset($route['middleware']) && $route['middleware'] !== "") {
                    $r->middleware($route['middleware']);
                }
                if (isset($route['domain']) && $route['domain'] !== "") {
                    $r->domain($route['domain']);
                }
                if (isset($route['name']) && $route['name'] !== "") {
                    $r->name($route['name']);
                }
                if (isset($route['prefix']) && $route['prefix'] !== "") {
                    $r->prefix($route['prefix']);
                }
            }
        } else {
            exit("请执行命令 php artisan app:generate-routing-cache-command 生成路由缓存文件！");
        }
    }
}

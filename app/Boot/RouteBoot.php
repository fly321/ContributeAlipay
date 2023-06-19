<?php

namespace App\Boot;

use App\Annotation\RequestMapping;
use App\Scan\Scanning as ScanningAlias;
use Illuminate\Support\Facades\Route;

class RouteBoot
{
    public static function handle(){

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
    }
}

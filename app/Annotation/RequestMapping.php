<?php

namespace App\Annotation;

use Illuminate\Support\Facades\Route;

#[\Attribute(\Attribute::TARGET_METHOD, \Attribute::TARGET_CLASS)]
class RequestMapping
{
    public function __construct(
        public string $path = "",
        public string $name = "",
        public string|array $method = "GET",
        public string|array $middleware = "",
        public string $domain = "",
        public string $prefix = "",
    )
    {

    }

}

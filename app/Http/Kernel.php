<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected array $middlewareGroups = [
        'web' => [\App\Http\Middleware\InjectUserImages::class,],

    ];

        protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
    ];
}

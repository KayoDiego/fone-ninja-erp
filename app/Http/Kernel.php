<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;

class Kernel extends HttpKernel
{
    protected $middleware = [];

    protected $middlewareGroups = [
        'web' => [
            SubstituteBindings::class
        ],
        'api' => [
            ThrottleRequests::class . ':api',
            SubstituteBindings::class
        ]
    ];

    protected $middlewareAliases = [];
}


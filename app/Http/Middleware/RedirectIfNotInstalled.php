<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RedirectIfNotInstalled
{
    public function handle(Request $request, Closure $next)
    {
        $lockFile = config('installer.lock_file');

        if (! File::exists($lockFile)) {
            return redirect(config('installer.route_prefix'));
        }

        return $next($request);
    }
}

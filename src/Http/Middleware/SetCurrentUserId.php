<?php

namespace Odinbi\ActivityLogsWithTrigger\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetCurrentUserId
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            DB::statement("SET @current_user_id = ?", [auth()->id()]);
        }

        return $next($request);
    }
}

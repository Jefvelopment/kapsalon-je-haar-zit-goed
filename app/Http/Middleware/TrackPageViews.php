<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackPageViews
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') && !$request->is('dashboard*') && !$request->is('login*') && !$request->is('register*')) {
            PageView::create([
                'url'     => $request->path(),
                'ip'      => $request->ip(),
                'user_id' => Auth::id(),
            ]);
        }

        return $next($request);
    }
}
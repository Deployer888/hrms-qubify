<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || ($user->plan <= 0 && $user->type == 'company')) {
            return redirect()->route('plans.index')->with('error', 'Please purchase the plan to get full access!!');
        }

        return $next($request);
    }
}

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

        // if (!$user || ($user->plan <= 0 && $user->type == 'company' && $user->type != 'super admin')) {
        //     return redirect()->route('plans.index')->with('error', 'Please purchase the plan to get full access!!');
        // }

        // return $next($request);
        // Skip plan check for non-authenticated users or super admin
        if (!$user || $user->type == 'super admin') {
            return $next($request);
        }

        // Only check plan for company users
        if ($user->type == 'company' && $user->plan <= 0) {
            return redirect()->route('plans.index')->with('error', 'Please purchase the plan to get full access!!');
        }

        return $next($request);
    }
}

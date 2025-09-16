<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Schema;
use App\Helpers\Helper;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
        View::composer('*', function ($view) {
            $allRoles = '';
            if(\Auth::check() && isset(\Auth::user()->employee)){
                $employee = \Auth::user()->employee;
                $allRoles = Helper::getUserRoles($employee);
            }
            $view->with('allRoles', $allRoles);
        });
    }
}

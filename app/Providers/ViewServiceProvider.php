<?php

namespace App\Providers;

use App\Models\Wedding;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (! View::shared('wedding')) {
                View::share('wedding', Wedding::first());
            }
        });
    }
}

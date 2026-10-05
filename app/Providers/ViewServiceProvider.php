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
                try {
                    $wedding = Wedding::first();
                } catch (\Throwable $e) {
                    report($e);
                    $wedding = null;
                }
                View::share('wedding', $wedding);
            }
        });
    }
}
<?php

namespace App\Providers;

use App\Support\DbFilesystemAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem as Flysystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Database-backed disk: keeps uploaded photos in the file_storage table
        // so they survive on hosts with a read-only filesystem (Vercel).
        Storage::extend('dbfiles', function ($app, $config) {
            $adapter = new DbFilesystemAdapter();

            return new FilesystemAdapter(new Flysystem($adapter), $adapter, $config);
        });

        // Generate https links when the site is served over https (e.g. on Vercel)
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}

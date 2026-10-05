<?php

/*
|--------------------------------------------------------------------------
| Vercel entry point
|--------------------------------------------------------------------------
|
| Every request that is not a static file in /public is routed here by
| vercel.json. This mirrors public/index.php, with two differences:
|
|  - Vercel's filesystem is read-only except /tmp, so Laravel's storage and
|    bootstrap cache folders are moved there.
|  - Sensible production defaults are applied for anything not already set
|    in the Vercel dashboard (dashboard environment variables always win).
|
*/

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$tmp = '/tmp';

$defaults = [
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'LOG_CHANNEL' => 'stderr',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'APP_CONFIG_CACHE' => "$tmp/bootstrap/cache/config.php",
    'APP_EVENTS_CACHE' => "$tmp/bootstrap/cache/events.php",
    'APP_PACKAGES_CACHE' => "$tmp/bootstrap/cache/packages.php",
    'APP_ROUTES_CACHE' => "$tmp/bootstrap/cache/routes.php",
    'APP_SERVICES_CACHE' => "$tmp/bootstrap/cache/services.php",
    'VIEW_COMPILED_PATH' => "$tmp/storage/framework/views",
];

foreach ($defaults as $key => $value) {
    if (getenv($key) === false && ! isset($_ENV[$key]) && ! isset($_SERVER[$key])) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

foreach ([
    "$tmp/bootstrap/cache",
    "$tmp/storage/app/public",
    "$tmp/storage/framework/cache/data",
    "$tmp/storage/framework/sessions",
    "$tmp/storage/framework/views",
    "$tmp/storage/logs",
] as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->useStoragePath("$tmp/storage");

$app->handleRequest(Request::capture());

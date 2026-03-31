<?php

// Force error reporting
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Illuminate\Http\Request;

// Let's create a fail-safe catch block for EVERY error
try {
    $storagePath = '/tmp/storage';

    if (!is_dir($storagePath)) {
        mkdir($storagePath . '/framework/sessions', 0777, true);
        mkdir($storagePath . '/framework/views', 0777, true);
        mkdir($storagePath . '/framework/cache/data', 0777, true);
        mkdir($storagePath . '/logs', 0777, true);
        mkdir($storagePath . '/bootstrap/cache', 0777, true);
    }

    if (!is_writable($storagePath)) {
        throw new \Exception("Storage path is not writable.");
    }

    putenv("APP_CONFIG_CACHE={$storagePath}/bootstrap/cache/config.php");
    putenv("APP_ROUTES_CACHE={$storagePath}/bootstrap/cache/routes.php");
    putenv("APP_SERVICES_CACHE={$storagePath}/bootstrap/cache/services.php");
    putenv("APP_PACKAGES_CACHE={$storagePath}/bootstrap/cache/packages.php");

    $_ENV['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
    putenv("VIEW_COMPILED_PATH={$_ENV['VIEW_COMPILED_PATH']}");

    define('LARAVEL_START', microtime(true));

    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        throw new \Exception("vendor/autoload.php is missing. Composer did not run on Vercel.");
    }

    require __DIR__.'/../vendor/autoload.php';

    // We will echo a debugging string right here.
    // If you see this string on the webpage, Laravel's core is failing AFTER this point!
    // echo "<h1>Vercel PHP is working! Starting Laravel now...</h1>";

    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->useStoragePath($storagePath);

    $app->handleRequest(Request::capture());

} catch (\Throwable $e) {
    // If ANY error happens, we FORCE the server to output it as a 200 OK so Vercel doesn't hide it with a 500 page
    http_response_code(200);
    echo "<h1>CRITICAL ERROR:</h1>";
    echo "<pre style='color:red;'>";
    echo $e->getMessage() . "\n\n";
    echo $e->getTraceAsString();
    echo "</pre>";
}

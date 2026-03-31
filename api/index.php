<?php

// Force error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fix temporary storage paths for Vercel
$storagePath = '/tmp/storage';

if (!is_dir($storagePath)) {
    mkdir($storagePath . '/framework/sessions', 0777, true);
    mkdir($storagePath . '/framework/views', 0777, true);
    mkdir($storagePath . '/framework/cache/data', 0777, true);
    mkdir($storagePath . '/logs', 0777, true);
    mkdir($storagePath . '/bootstrap/cache', 0777, true);
}

// Ensure the storage path is writable before we proceed
if (!is_writable($storagePath)) {
    die("Error: Storage directory is not writable on this Vercel instance.");
}

// Redirect cache variables to the tmp storage
putenv("APP_CONFIG_CACHE={$storagePath}/bootstrap/cache/config.php");
putenv("APP_ROUTES_CACHE={$storagePath}/bootstrap/cache/routes.php");
putenv("APP_SERVICES_CACHE={$storagePath}/bootstrap/cache/services.php");
putenv("APP_PACKAGES_CACHE={$storagePath}/bootstrap/cache/packages.php");

// Set the compiled views path to the writable tmp storage
$_ENV['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
putenv("VIEW_COMPILED_PATH={$_ENV['VIEW_COMPILED_PATH']}");

// Laravel Initializations mimicking public/index.php
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ensure autoload exists (composer install happened)
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die("Error: vendor/autoload.php is missing. Composer did not run successfully on Vercel.");
}

require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Dynamically override the storage path to our /tmp/storage map
$app->useStoragePath($storagePath);

// Handle the incoming request
$app->handleRequest(Request::capture());

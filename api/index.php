<?php

// Fix temporary storage paths for Vercel
$storagePath = '/tmp/storage';

if (!is_dir($storagePath)) {
    mkdir($storagePath . '/framework/sessions', 0755, true);
    mkdir($storagePath . '/framework/views', 0755, true);
    mkdir($storagePath . '/framework/cache', 0755, true);
    mkdir($storagePath . '/bootstrap/cache', 0755, true);
}

putenv("APP_CONFIG_CACHE={$storagePath}/bootstrap/cache/config.php");
putenv("APP_ROUTES_CACHE={$storagePath}/bootstrap/cache/routes.php");
putenv("APP_SERVICES_CACHE={$storagePath}/bootstrap/cache/services.php");
putenv("APP_PACKAGES_CACHE={$storagePath}/bootstrap/cache/packages.php");

// Forward Vercel requests to normal index.php
require __DIR__ . '/../public/index.php';

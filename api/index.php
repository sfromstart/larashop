<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);

// Create writable directories in /tmp (serverless read-only filesystem)
$storagePath = '/tmp/storage';
$bootstrapCachePath = '/tmp/bootstrap/cache';
foreach ([
    "$storagePath/app/public",
    "$storagePath/framework/cache/data",
    "$storagePath/framework/sessions",
    "$storagePath/framework/views",
    "$storagePath/logs",
    $bootstrapCachePath,
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Copy SQLite database to /tmp on cold start
$dbSource = $basePath . '/database/database.sqlite';
$dbTarget = '/tmp/database.sqlite';
if (!file_exists($dbTarget) && file_exists($dbSource)) {
    copy($dbSource, $dbTarget);
}

// Force HTTPS for Vercel reverse proxy
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;

// Register the Composer autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';
$app->useStoragePath($storagePath);
$app->useBootstrapPath('/tmp/bootstrap');

// Handle the request
$app->handleRequest(Request::capture());

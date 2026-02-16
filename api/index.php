<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);

// Create writable storage directories in /tmp (serverless filesystem)
$storagePath = '/tmp/storage';
foreach ([
    "$storagePath/app/public",
    "$storagePath/framework/cache/data",
    "$storagePath/framework/sessions",
    "$storagePath/framework/views",
    "$storagePath/logs",
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

// Register the Composer autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';
$app->useStoragePath($storagePath);

// Handle the request
$app->handleRequest(Request::capture());

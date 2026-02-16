<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Error reporting for debugging
ini_set('display_errors', '1');
error_reporting(E_ALL);

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

// Register the Composer autoloader
require $basePath . '/vendor/autoload.php';

try {
    // Bootstrap Laravel
    /** @var Application $app */
    $app = require_once $basePath . '/bootstrap/app.php';
    $app->useStoragePath($storagePath);
    $app->useBootstrapPath('/tmp/bootstrap');

    // Handle the request
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";

    // Check for previous exception
    if ($prev = $e->getPrevious()) {
        echo "\nPrevious: " . $prev->getMessage() . "\n";
        echo "File: " . $prev->getFile() . ":" . $prev->getLine() . "\n";
    }
}

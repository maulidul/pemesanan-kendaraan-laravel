<?php

/**
 * Router script untuk PHP built-in server.
 *
 * - File static yang ada di public/ akan disajikan langsung (return false).
 * - Semua request lain diarahkan ke public/index.php (Laravel).
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files (build assets, css, js, images, etc.) jika ada di public/
$staticFile = __DIR__ . $uri;
if ($uri !== '/' && file_exists($staticFile) && !is_dir($staticFile)) {
    return false; // PHP built-in server akan serve file static
}

// Otherwise route ke Laravel
require_once __DIR__ . '/index.php';
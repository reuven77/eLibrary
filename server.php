<?php

/**
 * PHP built-in server router for Railway.
 * Do not use Laravel's vendor server.php here: it uses getcwd() which is /app
 * when started with `-t public`, so it looks for /app/index.php and fatals.
 */
$publicPath = __DIR__.'/public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

require_once $publicPath.'/index.php';

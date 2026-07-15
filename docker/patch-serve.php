<?php

/**
 * Railway often runs `artisan serve --port=$PORT` without a shell, so $PORT
 * stays a literal string and PHP 8.4 fatals at `$port + $offset`.
 */
$file = __DIR__.'/../vendor/laravel/framework/src/Illuminate/Foundation/Console/ServeCommand.php';

if (! is_file($file)) {
    fwrite(STDERR, "ServeCommand.php not found\n");
    exit(1);
}

$contents = file_get_contents($file);
$search = 'return $port + $this->portOffset;';
$replace = 'return (int) ((is_numeric($port) ? $port : (getenv(\'PORT\') ?: 8000)) ?: 8000) + $this->portOffset;';

if (! str_contains($contents, $search)) {
    fwrite(STDERR, "ServeCommand patch target not found\n");
    exit(1);
}

file_put_contents($file, str_replace($search, $replace, $contents));
echo "Patched ServeCommand.php for Railway PORT handling\n";

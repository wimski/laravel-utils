<?php

declare(strict_types=1);

$directory = __DIR__ . DIRECTORY_SEPARATOR . 'helpers';

$files = array_diff(scandir($directory), ['.', '..']);

foreach ($files as $file) {
    $path = $directory . DIRECTORY_SEPARATOR . $file;

    if (pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
        continue;
    }

    require_once $path;
}

<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/helpers/functions.php';

spl_autoload_register(function ($class) {
    $class = preg_replace('/[^A-Za-z0-9_]/', '', (string) $class);
    if ($class === '') {
        return;
    }

    $paths = [
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/config/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

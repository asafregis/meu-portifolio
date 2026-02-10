<?php

spl_autoload_register(function ($class) {
    $base = __DIR__;
    $key = strtolower(trim($class));

    $map = [
        'connection' => $base . '/connection.class.php',
        'modalidade' => $base . '/modalidades.class.php',
        'alunos' => $base . '/alunos.class.php',
        'crudmodalidade' => $base . '/crudModalidade.php',
        'crudmodalidades' => $base . '/crudModalidade.php',
        'crudalunos' => $base . '/crudAlunos.php',
    ];

    if (isset($map[$key]) && is_file($map[$key])) {
        require_once $map[$key];
        return;
    }

    $candidates = [
        $base . '/' . $class . '.class.php',
        $base . '/' . strtolower($class) . '.class.php',
        $base . '/' . $class . '.php',
        $base . '/' . strtolower($class) . '.php',
    ];

    foreach ($candidates as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

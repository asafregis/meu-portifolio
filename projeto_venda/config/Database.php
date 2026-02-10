<?php

class Database
{
    private static $conn;

    public static function conectar()
    {
        if (self::$conn instanceof PDO) {
            return self::$conn;
        }

        $config = require __DIR__ . '/config.php';
        $db = $config['db'] ?? [];

        $host = $db['host'] ?? 'localhost';
        $name = $db['name'] ?? 'projeto_venda';
        $user = $db['user'] ?? 'root';
        $pass = $db['pass'] ?? '12345678';
        $charset = $db['charset'] ?? 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

        self::$conn = new PDO(
            $dsn,
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return self::$conn;
    }
}

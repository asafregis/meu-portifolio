<?php
abstract class Connection
{
    private $serverDB = 'mysql:host=localhost;dbname=db_dpo_asaf';
    private $user = 'root';
    private $pass = '12345678';

    protected function connect()
    {
        try {
            $conn = new PDO($this->serverDB, $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("SET NAMES utf8");
            return $conn;
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }
}

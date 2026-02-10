<?php

require_once __DIR__ . '/../config/Database.php';

class Professor
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }

    public function cadastrar($nome, $email, $cpf = null, $telefone = null, $titulacao = null)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO professores (nome, email, cpf, telefone, titulacao) VALUES (?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $nome,
            $email,
            $cpf ?: null,
            $telefone ?: null,
            $titulacao ?: null,
        ]);
    }

    public function atualizar($id, $nome, $email, $cpf = null, $telefone = null, $titulacao = null)
    {
        $stmt = $this->conn->prepare(
            'UPDATE professores SET nome=?, email=?, cpf=?, telefone=?, titulacao=? WHERE id=?'
        );

        return $stmt->execute([
            $nome,
            $email,
            $cpf ?: null,
            $telefone ?: null,
            $titulacao ?: null,
            $id,
        ]);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT id, nome, email, cpf, telefone, titulacao FROM professores WHERE id=?'
        );
        $stmt->execute([$id]);
        $professor = $stmt->fetch();
        return $professor ?: null;
    }

    public function listar()
    {
        return $this->conn->query(
            'SELECT id, nome, email, cpf, telefone, titulacao FROM professores ORDER BY nome'
        )->fetchAll();
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM professores WHERE id=?');
        return $stmt->execute([$id]);
    }

    public function contar()
    {
        return (int) $this->conn->query('SELECT COUNT(*) FROM professores')->fetchColumn();
    }
}


<?php
require_once __DIR__ . '/../config/Database.php';

class Aluno
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }

    public function cadastrar($nome, $email, $cpf = null, $telefone = null, $dataNascimento = null)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO alunos (nome, email, cpf, telefone, data_nascimento) VALUES (?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $nome,
            $email,
            $cpf ?: null,
            $telefone ?: null,
            $dataNascimento ?: null,
        ]);
    }

    public function atualizar($id, $nome, $email, $cpf = null, $telefone = null, $dataNascimento = null)
    {
        $stmt = $this->conn->prepare(
            'UPDATE alunos SET nome=?, email=?, cpf=?, telefone=?, data_nascimento=? WHERE id=?'
        );

        return $stmt->execute([
            $nome,
            $email,
            $cpf ?: null,
            $telefone ?: null,
            $dataNascimento ?: null,
            $id,
        ]);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT id, nome, email, cpf, telefone, data_nascimento FROM alunos WHERE id=?'
        );
        $stmt->execute([$id]);
        $aluno = $stmt->fetch();
        return $aluno ?: null;
    }

    public function listar()
    {
        return $this->conn->query(
            'SELECT id, nome, email, cpf, telefone, data_nascimento FROM alunos ORDER BY nome'
        )->fetchAll();
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM alunos WHERE id=?');
        return $stmt->execute([$id]);
    }

    public function contar()
    {
        return (int) $this->conn->query('SELECT COUNT(*) FROM alunos')->fetchColumn();
    }
}

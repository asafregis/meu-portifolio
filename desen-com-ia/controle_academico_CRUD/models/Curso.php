<?php

require_once __DIR__ . '/../config/Database.php';

class Curso
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }

    public function cadastrar($nome, $codigo, $cargaHoraria, $descricao = null)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO cursos (nome, codigo, carga_horaria, descricao) VALUES (?, ?, ?, ?)'
        );

        return $stmt->execute([
            $nome,
            $codigo,
            (int) $cargaHoraria,
            $descricao ?: null,
        ]);
    }

    public function atualizar($id, $nome, $codigo, $cargaHoraria, $descricao = null)
    {
        $stmt = $this->conn->prepare(
            'UPDATE cursos SET nome=?, codigo=?, carga_horaria=?, descricao=? WHERE id=?'
        );

        return $stmt->execute([
            $nome,
            $codigo,
            (int) $cargaHoraria,
            $descricao ?: null,
            $id,
        ]);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT id, nome, codigo, carga_horaria, descricao FROM cursos WHERE id=?'
        );
        $stmt->execute([$id]);
        $curso = $stmt->fetch();
        return $curso ?: null;
    }

    public function listar()
    {
        return $this->conn->query(
            'SELECT id, nome, codigo, carga_horaria, descricao FROM cursos ORDER BY nome'
        )->fetchAll();
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM cursos WHERE id=?');
        return $stmt->execute([$id]);
    }

    public function contar()
    {
        return (int) $this->conn->query('SELECT COUNT(*) FROM cursos')->fetchColumn();
    }
}


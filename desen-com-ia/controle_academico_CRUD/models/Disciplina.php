<?php

require_once __DIR__ . '/../config/Database.php';

class Disciplina
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }

    public function cadastrar($cursoId, $nome, $codigo, $cargaHoraria)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO disciplinas (curso_id, nome, codigo, carga_horaria) VALUES (?, ?, ?, ?)'
        );

        return $stmt->execute([
            (int) $cursoId,
            $nome,
            $codigo,
            (int) $cargaHoraria,
        ]);
    }

    public function atualizar($id, $cursoId, $nome, $codigo, $cargaHoraria)
    {
        $stmt = $this->conn->prepare(
            'UPDATE disciplinas SET curso_id=?, nome=?, codigo=?, carga_horaria=? WHERE id=?'
        );

        return $stmt->execute([
            (int) $cursoId,
            $nome,
            $codigo,
            (int) $cargaHoraria,
            (int) $id,
        ]);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT id, curso_id, nome, codigo, carga_horaria FROM disciplinas WHERE id=?'
        );
        $stmt->execute([(int) $id]);
        $disciplina = $stmt->fetch();
        return $disciplina ?: null;
    }

    public function listar()
    {
        return $this->conn->query(
            'SELECT d.id, d.curso_id, d.nome, d.codigo, d.carga_horaria, c.nome AS curso_nome, c.codigo AS curso_codigo
             FROM disciplinas d
             JOIN cursos c ON c.id = d.curso_id
             ORDER BY c.nome, d.nome'
        )->fetchAll();
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM disciplinas WHERE id=?');
        return $stmt->execute([(int) $id]);
    }

    public function contar()
    {
        return (int) $this->conn->query('SELECT COUNT(*) FROM disciplinas')->fetchColumn();
    }
}


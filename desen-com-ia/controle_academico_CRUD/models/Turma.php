<?php

require_once __DIR__ . '/../config/Database.php';

class Turma
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }

    public function cadastrar($disciplinaId, $professorId, $ano, $semestre, $turno = null, $sala = null, $vagas = 0)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO turmas (disciplina_id, professor_id, ano, semestre, turno, sala, vagas)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        $professorId = $professorId !== '' ? $professorId : null;

        return $stmt->execute([
            (int) $disciplinaId,
            $professorId !== null ? (int) $professorId : null,
            (int) $ano,
            (int) $semestre,
            $turno ?: null,
            $sala ?: null,
            (int) $vagas,
        ]);
    }

    public function atualizar($id, $disciplinaId, $professorId, $ano, $semestre, $turno = null, $sala = null, $vagas = 0)
    {
        $stmt = $this->conn->prepare(
            'UPDATE turmas
             SET disciplina_id=?, professor_id=?, ano=?, semestre=?, turno=?, sala=?, vagas=?
             WHERE id=?'
        );

        $professorId = $professorId !== '' ? $professorId : null;

        return $stmt->execute([
            (int) $disciplinaId,
            $professorId !== null ? (int) $professorId : null,
            (int) $ano,
            (int) $semestre,
            $turno ?: null,
            $sala ?: null,
            (int) $vagas,
            (int) $id,
        ]);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT id, disciplina_id, professor_id, ano, semestre, turno, sala, vagas
             FROM turmas WHERE id=?'
        );
        $stmt->execute([(int) $id]);
        $turma = $stmt->fetch();
        return $turma ?: null;
    }

    public function listar()
    {
        return $this->conn->query(
            'SELECT
                t.id,
                t.disciplina_id,
                t.professor_id,
                t.ano,
                t.semestre,
                t.turno,
                t.sala,
                t.vagas,
                d.nome AS disciplina_nome,
                d.codigo AS disciplina_codigo,
                c.nome AS curso_nome,
                c.codigo AS curso_codigo,
                p.nome AS professor_nome
             FROM turmas t
             JOIN disciplinas d ON d.id = t.disciplina_id
             JOIN cursos c ON c.id = d.curso_id
             LEFT JOIN professores p ON p.id = t.professor_id
             ORDER BY t.ano DESC, t.semestre DESC, c.nome, d.nome'
        )->fetchAll();
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM turmas WHERE id=?');
        return $stmt->execute([(int) $id]);
    }

    public function contar()
    {
        return (int) $this->conn->query('SELECT COUNT(*) FROM turmas')->fetchColumn();
    }
}


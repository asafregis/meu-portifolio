<?php

require_once __DIR__ . '/../config/Database.php';

class Matricula
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }

    public function matricular($alunoId, $turmaId, $status = 'ATIVA')
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO matriculas (aluno_id, turma_id, status) VALUES (?, ?, ?)'
        );

        return $stmt->execute([
            (int) $alunoId,
            (int) $turmaId,
            $status,
        ]);
    }

    public function atualizarStatus($id, $status)
    {
        $stmt = $this->conn->prepare('UPDATE matriculas SET status=? WHERE id=?');
        return $stmt->execute([
            $status,
            (int) $id,
        ]);
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM matriculas WHERE id=?');
        return $stmt->execute([(int) $id]);
    }

    public function listar()
    {
        return $this->conn->query(
            'SELECT
                m.id,
                m.aluno_id,
                m.turma_id,
                m.status,
                m.data_matricula,
                a.nome AS aluno_nome,
                a.email AS aluno_email,
                c.nome AS curso_nome,
                c.codigo AS curso_codigo,
                d.nome AS disciplina_nome,
                d.codigo AS disciplina_codigo,
                t.ano,
                t.semestre,
                t.turno,
                t.sala,
                p.nome AS professor_nome
             FROM matriculas m
             JOIN alunos a ON a.id = m.aluno_id
             JOIN turmas t ON t.id = m.turma_id
             JOIN disciplinas d ON d.id = t.disciplina_id
             JOIN cursos c ON c.id = d.curso_id
             LEFT JOIN professores p ON p.id = t.professor_id
             ORDER BY m.data_matricula DESC, a.nome'
        )->fetchAll();
    }

    public function contar()
    {
        return (int) $this->conn->query('SELECT COUNT(*) FROM matriculas')->fetchColumn();
    }
}


<?php

require_once __DIR__ . '/bootstrap.php';

$model = new Matricula();
$alunoModel = new Aluno();
$turmaModel = new Turma();

$alunos = $alunoModel->listar();
$turmas = $turmaModel->listar();

$statusList = [
    'ATIVA' => 'Ativa',
    'TRANCADA' => 'Trancada',
    'CANCELADA' => 'Cancelada',
    'CONCLUIDA' => 'Concluída',
];

$turnoMap = [
    'MANHA' => 'Manhã',
    'TARDE' => 'Tarde',
    'NOITE' => 'Noite',
];

function turma_label($t, $turnoMap)
{
    $parts = [];
    $parts[] = ($t['curso_codigo'] ?? '') . ' • ' . ($t['disciplina_codigo'] ?? '');
    $parts[] = ($t['ano'] ?? '') . '/' . ($t['semestre'] ?? '');
    if (!empty($t['turno'])) {
        $parts[] = $turnoMap[$t['turno']] ?? $t['turno'];
    }
    if (!empty($t['sala'])) {
        $parts[] = 'Sala ' . $t['sala'];
    }
    if (!empty($t['professor_nome'])) {
        $parts[] = $t['professor_nome'];
    }
    return trim(implode(' • ', array_filter($parts)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $alunoId = (int) ($_POST['aluno_id'] ?? 0);
            $turmaId = (int) ($_POST['turma_id'] ?? 0);
            $status = (string) ($_POST['status'] ?? 'ATIVA');

            if ($alunoId <= 0 || $turmaId <= 0) {
                flash_set('Selecione aluno e turma.', 'warning');
                redirect('matriculas.php');
            }

            if (!isset($statusList[$status])) {
                flash_set('Status inválido.', 'warning');
                redirect('matriculas.php');
            }

            $model->matricular($alunoId, $turmaId, $status);
            flash_set('Matrícula registrada com sucesso.');
            redirect('matriculas.php');
        }

        if ($action === 'update_status') {
            $id = (int) ($_POST['id'] ?? 0);
            $status = (string) ($_POST['status'] ?? '');

            if ($id <= 0 || !isset($statusList[$status])) {
                flash_set('Dados inválidos.', 'warning');
                redirect('matriculas.php');
            }

            $model->atualizarStatus($id, $status);
            flash_set('Status atualizado.');
            redirect('matriculas.php');
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('matriculas.php');
            }

            $model->excluir($id);
            flash_set('Matrícula excluída.');
            redirect('matriculas.php');
        }
    } catch (PDOException $e) {
        $isDuplicate = isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
        flash_set($isDuplicate ? 'Este aluno já está matriculado nesta turma.' : 'Erro ao salvar no banco.', 'error');
        redirect('matriculas.php');
    }
}

$matriculas = $model->listar();

$flash = flash_get();
$pageTitle = 'Matrículas';
$pageSubtitle = 'Matricule alunos em turmas e acompanhe o status.';
$active = 'matriculas';
require __DIR__ . '/partials/header.php';
?>

<?php if (count($alunos) === 0 || count($turmas) === 0): ?>
    <div class="card panel-card">
        <div class="card-content">
            <span class="card-title">Antes de matricular…</span>
            <p>
                Você precisa ter <a href="alunos.php">alunos</a> e <a href="turmas.php">turmas</a> cadastrados.
            </p>
        </div>
    </div>
<?php endif ?>

<div class="row">
    <div class="col s12 m5">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Nova Matrícula</span>
                <form method="post">
                    <input type="hidden" name="action" value="create">

                    <div class="input-field">
                        <select name="aluno_id" required>
                            <option value="" disabled selected>Selecione um aluno</option>
                            <?php foreach ($alunos as $a): ?>
                                <option value="<?= h($a['id']) ?>"><?= h($a['nome']) ?> • <?= h($a['email']) ?></option>
                            <?php endforeach ?>
                        </select>
                        <label>Aluno</label>
                    </div>

                    <div class="input-field">
                        <select name="turma_id" required>
                            <option value="" disabled selected>Selecione uma turma</option>
                            <?php foreach ($turmas as $t): ?>
                                <option value="<?= h($t['id']) ?>">
                                    <?= h(turma_label($t, $turnoMap)) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <label>Turma</label>
                    </div>

                    <div class="input-field">
                        <select name="status" required>
                            <?php foreach ($statusList as $val => $label): ?>
                                <option value="<?= h($val) ?>" <?= $val === 'ATIVA' ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach ?>
                        </select>
                        <label>Status</label>
                    </div>

                    <div class="form-actions">
                        <button class="btn waves-effect waves-light" type="submit">Matricular</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col s12 m7">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Lista de Matrículas</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Turma</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matriculas as $m): ?>
                                <tr>
                                    <td>
                                        <strong><?= h($m['aluno_nome']) ?></strong>
                                        <div class="muted-text"><?= h($m['aluno_email']) ?></div>
                                    </td>
                                    <td>
                                        <?= h($m['curso_codigo']) ?> • <?= h($m['disciplina_codigo']) ?>
                                        <div class="muted-text">
                                            <?= h($m['ano']) ?>/<?= h($m['semestre']) ?>
                                            <?= $m['turno'] ? ' • ' . h($turnoMap[$m['turno']] ?? $m['turno']) : '' ?>
                                            <?= $m['sala'] ? ' • Sala ' . h($m['sala']) : '' ?>
                                            <?= $m['professor_nome'] ? ' • ' . h($m['professor_nome']) : '' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <form method="post" class="status-form">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= h($m['id']) ?>">
                                            <select name="status" class="browser-default">
                                                <?php foreach ($statusList as $val => $label): ?>
                                                    <option value="<?= h($val) ?>" <?= $m['status'] === $val ? 'selected' : '' ?>>
                                                        <?= h($label) ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                            <button class="btn-small waves-effect" type="submit">OK</button>
                                        </form>
                                    </td>
                                    <td class="table-actions">
                                        <form method="post" class="inline-form" onsubmit="return confirm('Excluir esta matrícula?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= h($m['id']) ?>">
                                            <button class="btn-small red darken-1 waves-effect" type="submit">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($matriculas) === 0): ?>
                                <tr>
                                    <td colspan="4">Nenhuma matrícula registrada.</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

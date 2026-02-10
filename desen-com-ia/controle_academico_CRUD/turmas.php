<?php

require_once __DIR__ . '/bootstrap.php';

$model = new Turma();
$disciplinaModel = new Disciplina();
$professorModel = new Professor();

$disciplinas = $disciplinaModel->listar();
$professores = $professorModel->listar();

$turnos = [
    '' => 'Sem turno',
    'MANHA' => 'Manhã',
    'TARDE' => 'Tarde',
    'NOITE' => 'Noite',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $disciplinaId = (int) ($_POST['disciplina_id'] ?? 0);
            $professorId = (string) ($_POST['professor_id'] ?? '');
            $ano = (int) ($_POST['ano'] ?? 0);
            $semestre = (int) ($_POST['semestre'] ?? 0);
            $turno = (string) ($_POST['turno'] ?? '');
            $sala = trim($_POST['sala'] ?? '');
            $vagas = (int) ($_POST['vagas'] ?? 0);

            if ($disciplinaId <= 0 || $ano <= 0 || !in_array($semestre, [1, 2], true)) {
                flash_set('Preencha disciplina, ano e semestre.', 'warning');
                redirect('turmas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($ano < 2000 || $ano > 2100) {
                flash_set('Ano precisa estar entre 2000 e 2100.', 'warning');
                redirect('turmas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($turno !== '' && !isset($turnos[$turno])) {
                flash_set('Turno inválido.', 'warning');
                redirect('turmas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($vagas < 0) {
                flash_set('Vagas não pode ser negativo.', 'warning');
                redirect('turmas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($action === 'create') {
                $model->cadastrar($disciplinaId, $professorId, $ano, $semestre, $turno ?: null, $sala ?: null, $vagas);
                flash_set('Turma criada com sucesso.');
                redirect('turmas.php');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('turmas.php');
            }

            $model->atualizar($id, $disciplinaId, $professorId, $ano, $semestre, $turno ?: null, $sala ?: null, $vagas);
            flash_set('Turma atualizada com sucesso.');
            redirect('turmas.php');
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('turmas.php');
            }

            $model->excluir($id);
            flash_set('Turma excluída.');
            redirect('turmas.php');
        }
    } catch (PDOException $e) {
        flash_set('Erro ao salvar no banco.', 'error');
        redirect('turmas.php');
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? $model->buscarPorId($editId) : null;
if ($editId > 0 && !$editing) {
    flash_set('Turma não encontrada.', 'error');
    redirect('turmas.php');
}

$turmas = $model->listar();

$flash = flash_get();
$pageTitle = 'Turmas';
$pageSubtitle = 'Turmas ligam disciplina + professor + período.';
$active = 'turmas';
require __DIR__ . '/partials/header.php';
?>

<?php if (count($disciplinas) === 0): ?>
    <div class="card panel-card">
        <div class="card-content">
            <span class="card-title">Antes de criar turmas…</span>
            <p>Cadastre disciplinas em <a href="disciplinas.php">Disciplinas</a>.</p>
        </div>
    </div>
<?php endif ?>

<div class="row">
    <div class="col s12 m5">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title"><?= $editing ? 'Editar Turma' : 'Criar Turma' ?></span>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?= h($editing['id']) ?>">
                    <?php endif ?>

                    <div class="input-field">
                        <select name="disciplina_id" required>
                            <option value="" disabled <?= !$editing ? 'selected' : '' ?>>Selecione uma disciplina</option>
                            <?php foreach ($disciplinas as $d): ?>
                                <?php $selected = (int) ($editing['disciplina_id'] ?? 0) === (int) $d['id']; ?>
                                <option value="<?= h($d['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                                    <?= h($d['curso_codigo']) ?> • <?= h($d['curso_nome']) ?> — <?= h($d['codigo']) ?> • <?= h($d['nome']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <label>Disciplina</label>
                    </div>

                    <div class="input-field">
                        <select name="professor_id">
                            <option value="" <?= !$editing || ($editing['professor_id'] ?? null) === null ? 'selected' : '' ?>>Sem professor</option>
                            <?php foreach ($professores as $p): ?>
                                <?php $selected = (int) ($editing['professor_id'] ?? 0) === (int) $p['id']; ?>
                                <option value="<?= h($p['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                                    <?= h($p['nome']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <label>Professor (opcional)</label>
                    </div>

                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="ano" name="ano" type="number" min="2000" max="2100" required value="<?= h($editing['ano'] ?? date('Y')) ?>">
                            <label for="ano" class="active">Ano</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <select name="semestre" required>
                                <option value="" disabled <?= !$editing ? 'selected' : '' ?>>Semestre</option>
                                <option value="1" <?= (int) ($editing['semestre'] ?? 0) === 1 ? 'selected' : '' ?>>1º</option>
                                <option value="2" <?= (int) ($editing['semestre'] ?? 0) === 2 ? 'selected' : '' ?>>2º</option>
                            </select>
                            <label>Semestre</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12 m6">
                            <select name="turno">
                                <?php foreach ($turnos as $val => $label): ?>
                                    <?php
                                    $selected = (string) ($editing['turno'] ?? '') === (string) $val;
                                    ?>
                                    <option value="<?= h($val) ?>" <?= $selected ? 'selected' : '' ?>><?= h($label) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label>Turno</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="sala" name="sala" type="text" value="<?= h($editing['sala'] ?? '') ?>">
                            <label for="sala" class="<?= ($editing && ($editing['sala'] ?? '') !== '') ? 'active' : '' ?>">Sala (opcional)</label>
                        </div>
                    </div>

                    <div class="input-field">
                        <input id="vagas" name="vagas" type="number" min="0" required value="<?= h($editing['vagas'] ?? 0) ?>">
                        <label for="vagas" class="active">Vagas</label>
                    </div>

                    <div class="form-actions">
                        <button class="btn waves-effect waves-light" type="submit">
                            <?= $editing ? 'Salvar' : 'Criar' ?>
                        </button>
                        <?php if ($editing): ?>
                            <a class="btn-flat waves-effect" href="turmas.php">Cancelar</a>
                        <?php endif ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col s12 m7">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Lista de Turmas</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Disciplina</th>
                                <th>Professor</th>
                                <th>Período</th>
                                <th>Vagas</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($turmas as $t): ?>
                                <tr>
                                    <td><?= h($t['curso_codigo']) ?> • <?= h($t['curso_nome']) ?></td>
                                    <td><?= h($t['disciplina_codigo']) ?> • <?= h($t['disciplina_nome']) ?></td>
                                    <td><?= h($t['professor_nome'] ?: '—') ?></td>
                                    <td>
                                        <?= h($t['ano']) ?>/<?= h($t['semestre']) ?>
                                        <?= $t['turno'] ? ' • ' . h($turnos[$t['turno']] ?? $t['turno']) : '' ?>
                                        <?= $t['sala'] ? ' • Sala ' . h($t['sala']) : '' ?>
                                    </td>
                                    <td><?= h($t['vagas']) ?></td>
                                    <td class="table-actions">
                                        <a class="btn-small waves-effect" href="turmas.php?edit=<?= h($t['id']) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <form method="post" class="inline-form" onsubmit="return confirm('Excluir esta turma? Isso também remove matrículas relacionadas.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= h($t['id']) ?>">
                                            <button class="btn-small red darken-1 waves-effect" type="submit">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($turmas) === 0): ?>
                                <tr>
                                    <td colspan="6">Nenhuma turma cadastrada.</td>
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


<?php

require_once __DIR__ . '/bootstrap.php';

$model = new Disciplina();
$cursoModel = new Curso();
$cursos = $cursoModel->listar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $cursoId = (int) ($_POST['curso_id'] ?? 0);
            $nome = trim($_POST['nome'] ?? '');
            $codigo = strtoupper(trim($_POST['codigo'] ?? ''));
            $cargaHoraria = (int) ($_POST['carga_horaria'] ?? 0);

            if ($cursoId <= 0 || $nome === '' || $codigo === '' || $cargaHoraria <= 0) {
                flash_set('Preencha curso, nome, código e carga horária (> 0).', 'warning');
                redirect('disciplinas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($action === 'create') {
                $model->cadastrar($cursoId, $nome, $codigo, $cargaHoraria);
                flash_set('Disciplina cadastrada com sucesso.');
                redirect('disciplinas.php');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('disciplinas.php');
            }

            $model->atualizar($id, $cursoId, $nome, $codigo, $cargaHoraria);
            flash_set('Disciplina atualizada com sucesso.');
            redirect('disciplinas.php');
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('disciplinas.php');
            }

            $model->excluir($id);
            flash_set('Disciplina excluída.');
            redirect('disciplinas.php');
        }
    } catch (PDOException $e) {
        $isDuplicate = isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
        flash_set($isDuplicate ? 'Código da disciplina já cadastrado neste curso.' : 'Erro ao salvar no banco.', 'error');
        redirect('disciplinas.php');
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? $model->buscarPorId($editId) : null;
if ($editId > 0 && !$editing) {
    flash_set('Disciplina não encontrada.', 'error');
    redirect('disciplinas.php');
}

$disciplinas = $model->listar();

$flash = flash_get();
$pageTitle = 'Disciplinas';
$pageSubtitle = 'Disciplinas pertencem a um curso.';
$active = 'disciplinas';
require __DIR__ . '/partials/header.php';
?>

<?php if (count($cursos) === 0): ?>
    <div class="card panel-card">
        <div class="card-content">
            <span class="card-title">Antes de cadastrar disciplinas…</span>
            <p>Cadastre pelo menos um curso em <a href="cursos.php">Cursos</a>.</p>
        </div>
    </div>
<?php endif ?>

<div class="row">
    <div class="col s12 m5">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title"><?= $editing ? 'Editar Disciplina' : 'Cadastrar Disciplina' ?></span>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?= h($editing['id']) ?>">
                    <?php endif ?>

                    <div class="input-field">
                        <select name="curso_id" required>
                            <option value="" disabled <?= !$editing ? 'selected' : '' ?>>Selecione um curso</option>
                            <?php foreach ($cursos as $c): ?>
                                <?php $selected = (int) ($editing['curso_id'] ?? 0) === (int) $c['id']; ?>
                                <option value="<?= h($c['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                                    <?= h($c['codigo']) ?> • <?= h($c['nome']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <label>Curso</label>
                    </div>

                    <div class="input-field">
                        <input id="nome" name="nome" type="text" required value="<?= h($editing['nome'] ?? '') ?>">
                        <label for="nome" class="<?= ($editing && ($editing['nome'] ?? '') !== '') ? 'active' : '' ?>">Nome</label>
                    </div>

                    <div class="input-field">
                        <input id="codigo" name="codigo" type="text" required value="<?= h($editing['codigo'] ?? '') ?>">
                        <label for="codigo" class="<?= ($editing && ($editing['codigo'] ?? '') !== '') ? 'active' : '' ?>">Código</label>
                    </div>

                    <div class="input-field">
                        <input id="carga_horaria" name="carga_horaria" type="number" min="1" required value="<?= h($editing['carga_horaria'] ?? '') ?>">
                        <label for="carga_horaria" class="<?= ($editing && ($editing['carga_horaria'] ?? '') !== '') ? 'active' : '' ?>">Carga horária</label>
                    </div>

                    <div class="form-actions">
                        <button class="btn waves-effect waves-light" type="submit">
                            <?= $editing ? 'Salvar' : 'Cadastrar' ?>
                        </button>
                        <?php if ($editing): ?>
                            <a class="btn-flat waves-effect" href="disciplinas.php">Cancelar</a>
                        <?php endif ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col s12 m7">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Lista de Disciplinas</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Código</th>
                                <th>Disciplina</th>
                                <th>C.H.</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disciplinas as $d): ?>
                                <tr>
                                    <td><?= h($d['curso_codigo']) ?> • <?= h($d['curso_nome']) ?></td>
                                    <td><?= h($d['codigo']) ?></td>
                                    <td><?= h($d['nome']) ?></td>
                                    <td><?= h($d['carga_horaria']) ?></td>
                                    <td class="table-actions">
                                        <a class="btn-small waves-effect" href="disciplinas.php?edit=<?= h($d['id']) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <form method="post" class="inline-form" onsubmit="return confirm('Excluir esta disciplina? Isso também remove turmas e matrículas relacionadas.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= h($d['id']) ?>">
                                            <button class="btn-small red darken-1 waves-effect" type="submit">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($disciplinas) === 0): ?>
                                <tr>
                                    <td colspan="5">Nenhuma disciplina cadastrada.</td>
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


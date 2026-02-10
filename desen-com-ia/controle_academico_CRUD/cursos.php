<?php

require_once __DIR__ . '/bootstrap.php';

$model = new Curso();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $nome = trim($_POST['nome'] ?? '');
            $codigo = strtoupper(trim($_POST['codigo'] ?? ''));
            $cargaHoraria = (int) ($_POST['carga_horaria'] ?? 0);
            $descricao = trim($_POST['descricao'] ?? '');

            if ($nome === '' || $codigo === '' || $cargaHoraria <= 0) {
                flash_set('Preencha nome, código e carga horária (> 0).', 'warning');
                redirect('cursos.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($action === 'create') {
                $model->cadastrar($nome, $codigo, $cargaHoraria, $descricao ?: null);
                flash_set('Curso cadastrado com sucesso.');
                redirect('cursos.php');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('cursos.php');
            }

            $model->atualizar($id, $nome, $codigo, $cargaHoraria, $descricao ?: null);
            flash_set('Curso atualizado com sucesso.');
            redirect('cursos.php');
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('cursos.php');
            }

            $model->excluir($id);
            flash_set('Curso excluído.');
            redirect('cursos.php');
        }
    } catch (PDOException $e) {
        $isDuplicate = isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
        flash_set($isDuplicate ? 'Código do curso já cadastrado.' : 'Erro ao salvar no banco.', 'error');
        redirect('cursos.php');
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? $model->buscarPorId($editId) : null;
if ($editId > 0 && !$editing) {
    flash_set('Curso não encontrado.', 'error');
    redirect('cursos.php');
}

$cursos = $model->listar();

$flash = flash_get();
$pageTitle = 'Cursos';
$pageSubtitle = 'Cadastro de cursos (ex.: Sistemas de Informação, Enfermagem...).';
$active = 'cursos';
require __DIR__ . '/partials/header.php';
?>

<div class="row">
    <div class="col s12 m5">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title"><?= $editing ? 'Editar Curso' : 'Cadastrar Curso' ?></span>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?= h($editing['id']) ?>">
                    <?php endif ?>

                    <div class="input-field">
                        <input id="nome" name="nome" type="text" required value="<?= h($editing['nome'] ?? '') ?>">
                        <label for="nome" class="<?= ($editing && ($editing['nome'] ?? '') !== '') ? 'active' : '' ?>">Nome</label>
                    </div>

                    <div class="input-field">
                        <input id="codigo" name="codigo" type="text" required value="<?= h($editing['codigo'] ?? '') ?>">
                        <label for="codigo" class="<?= ($editing && ($editing['codigo'] ?? '') !== '') ? 'active' : '' ?>">Código (único)</label>
                    </div>

                    <div class="input-field">
                        <input id="carga_horaria" name="carga_horaria" type="number" min="1" required value="<?= h($editing['carga_horaria'] ?? '') ?>">
                        <label for="carga_horaria" class="<?= ($editing && ($editing['carga_horaria'] ?? '') !== '') ? 'active' : '' ?>">Carga horária</label>
                    </div>

                    <div class="input-field">
                        <textarea id="descricao" name="descricao" class="materialize-textarea"><?= h($editing['descricao'] ?? '') ?></textarea>
                        <label for="descricao" class="<?= ($editing && ($editing['descricao'] ?? '') !== '') ? 'active' : '' ?>">Descrição (opcional)</label>
                    </div>

                    <div class="form-actions">
                        <button class="btn waves-effect waves-light" type="submit">
                            <?= $editing ? 'Salvar' : 'Cadastrar' ?>
                        </button>
                        <?php if ($editing): ?>
                            <a class="btn-flat waves-effect" href="cursos.php">Cancelar</a>
                        <?php endif ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col s12 m7">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Lista de Cursos</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>C.H.</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $c): ?>
                                <tr>
                                    <td><?= h($c['codigo']) ?></td>
                                    <td><?= h($c['nome']) ?></td>
                                    <td><?= h($c['carga_horaria']) ?></td>
                                    <td class="table-actions">
                                        <a class="btn-small waves-effect" href="cursos.php?edit=<?= h($c['id']) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <form method="post" class="inline-form" onsubmit="return confirm('Excluir este curso? Isso também remove disciplinas e turmas relacionadas.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= h($c['id']) ?>">
                                            <button class="btn-small red darken-1 waves-effect" type="submit">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($cursos) === 0): ?>
                                <tr>
                                    <td colspan="4">Nenhum curso cadastrado.</td>
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


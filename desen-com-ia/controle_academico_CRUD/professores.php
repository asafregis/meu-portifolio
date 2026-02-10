<?php

require_once __DIR__ . '/bootstrap.php';

$model = new Professor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $cpf = preg_replace('/\D+/', '', (string) ($_POST['cpf'] ?? ''));
            $telefone = trim($_POST['telefone'] ?? '');
            $titulacao = trim($_POST['titulacao'] ?? '');

            if ($nome === '' || $email === '') {
                flash_set('Preencha nome e email.', 'warning');
                redirect('professores.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash_set('Email inválido.', 'warning');
                redirect('professores.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($cpf !== '' && strlen($cpf) !== 11) {
                flash_set('CPF precisa ter 11 dígitos (apenas números).', 'warning');
                redirect('professores.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($action === 'create') {
                $model->cadastrar($nome, $email, $cpf ?: null, $telefone ?: null, $titulacao ?: null);
                flash_set('Professor cadastrado com sucesso.');
                redirect('professores.php');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('professores.php');
            }

            $model->atualizar($id, $nome, $email, $cpf ?: null, $telefone ?: null, $titulacao ?: null);
            flash_set('Professor atualizado com sucesso.');
            redirect('professores.php');
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('professores.php');
            }

            $model->excluir($id);
            flash_set('Professor excluído.');
            redirect('professores.php');
        }
    } catch (PDOException $e) {
        $isDuplicate = isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
        flash_set($isDuplicate ? 'Email/CPF já cadastrado.' : 'Erro ao salvar no banco.', 'error');
        redirect('professores.php');
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? $model->buscarPorId($editId) : null;
if ($editId > 0 && !$editing) {
    flash_set('Professor não encontrado.', 'error');
    redirect('professores.php');
}

$professores = $model->listar();

$flash = flash_get();
$pageTitle = 'Professores';
$pageSubtitle = 'Cadastro e gerenciamento de professores.';
$active = 'professores';
require __DIR__ . '/partials/header.php';
?>

<div class="row">
    <div class="col s12 m5">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title"><?= $editing ? 'Editar Professor' : 'Cadastrar Professor' ?></span>
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
                        <input id="email" name="email" type="email" required value="<?= h($editing['email'] ?? '') ?>">
                        <label for="email" class="<?= ($editing && ($editing['email'] ?? '') !== '') ? 'active' : '' ?>">Email</label>
                    </div>

                    <div class="input-field">
                        <input id="cpf" name="cpf" type="text" inputmode="numeric" maxlength="14" value="<?= h($editing['cpf'] ?? '') ?>">
                        <label for="cpf" class="<?= ($editing && ($editing['cpf'] ?? '') !== '') ? 'active' : '' ?>">CPF (opcional)</label>
                    </div>

                    <div class="input-field">
                        <input id="telefone" name="telefone" type="text" value="<?= h($editing['telefone'] ?? '') ?>">
                        <label for="telefone" class="<?= ($editing && ($editing['telefone'] ?? '') !== '') ? 'active' : '' ?>">Telefone (opcional)</label>
                    </div>

                    <div class="input-field">
                        <input id="titulacao" name="titulacao" type="text" value="<?= h($editing['titulacao'] ?? '') ?>">
                        <label for="titulacao" class="<?= ($editing && ($editing['titulacao'] ?? '') !== '') ? 'active' : '' ?>">Titulação (opcional)</label>
                    </div>

                    <div class="form-actions">
                        <button class="btn waves-effect waves-light" type="submit">
                            <?= $editing ? 'Salvar' : 'Cadastrar' ?>
                        </button>
                        <?php if ($editing): ?>
                            <a class="btn-flat waves-effect" href="professores.php">Cancelar</a>
                        <?php endif ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col s12 m7">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Lista de Professores</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Titulação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($professores as $p): ?>
                                <tr>
                                    <td><?= h($p['nome']) ?></td>
                                    <td><?= h($p['email']) ?></td>
                                    <td><?= h($p['titulacao'] ?: '-') ?></td>
                                    <td class="table-actions">
                                        <a class="btn-small waves-effect" href="professores.php?edit=<?= h($p['id']) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <form method="post" class="inline-form" onsubmit="return confirm('Excluir este professor?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= h($p['id']) ?>">
                                            <button class="btn-small red darken-1 waves-effect" type="submit">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($professores) === 0): ?>
                                <tr>
                                    <td colspan="4">Nenhum professor cadastrado.</td>
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


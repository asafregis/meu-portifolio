<?php

require_once __DIR__ . '/bootstrap.php';

$model = new Aluno();

function validar_data($data)
{
    if ($data === null || $data === '') {
        return true;
    }

    $dt = DateTime::createFromFormat('Y-m-d', $data);
    return $dt && $dt->format('Y-m-d') === $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $cpf = preg_replace('/\D+/', '', (string) ($_POST['cpf'] ?? ''));
            $telefone = trim($_POST['telefone'] ?? '');
            $dataNascimento = trim($_POST['data_nascimento'] ?? '');

            if ($nome === '' || $email === '') {
                flash_set('Preencha nome e email.', 'warning');
                redirect('alunos.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash_set('Email inválido.', 'warning');
                redirect('alunos.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($cpf !== '' && strlen($cpf) !== 11) {
                flash_set('CPF precisa ter 11 dígitos (apenas números).', 'warning');
                redirect('alunos.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if (!validar_data($dataNascimento)) {
                flash_set('Data de nascimento inválida.', 'warning');
                redirect('alunos.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            $dataNascimento = $dataNascimento !== '' ? $dataNascimento : null;

            if ($action === 'create') {
                $model->cadastrar($nome, $email, $cpf ?: null, $telefone ?: null, $dataNascimento);
                flash_set('Aluno cadastrado com sucesso.');
                redirect('alunos.php');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('alunos.php');
            }

            $model->atualizar($id, $nome, $email, $cpf ?: null, $telefone ?: null, $dataNascimento);
            flash_set('Aluno atualizado com sucesso.');
            redirect('alunos.php');
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID inválido.', 'error');
                redirect('alunos.php');
            }

            $model->excluir($id);
            flash_set('Aluno excluído.');
            redirect('alunos.php');
        }
    } catch (PDOException $e) {
        $isDuplicate = isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
        flash_set($isDuplicate ? 'Email/CPF já cadastrado.' : 'Erro ao salvar no banco.', 'error');
        redirect('alunos.php');
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? $model->buscarPorId($editId) : null;
if ($editId > 0 && !$editing) {
    flash_set('Aluno não encontrado.', 'error');
    redirect('alunos.php');
}

$alunos = $model->listar();

$flash = flash_get();
$pageTitle = 'Alunos';
$pageSubtitle = 'Cadastro e gerenciamento de alunos.';
$active = 'alunos';
require __DIR__ . '/partials/header.php';
?>

<div class="row">
    <div class="col s12 m5">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title"><?= $editing ? 'Editar Aluno' : 'Cadastrar Aluno' ?></span>
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
                        <input id="data_nascimento" name="data_nascimento" type="date" value="<?= h($editing['data_nascimento'] ?? '') ?>">
                        <label for="data_nascimento" class="active">Data de nascimento (opcional)</label>
                    </div>

                    <div class="form-actions">
                        <button class="btn waves-effect waves-light" type="submit">
                            <?= $editing ? 'Salvar' : 'Cadastrar' ?>
                        </button>
                        <?php if ($editing): ?>
                            <a class="btn-flat waves-effect" href="alunos.php">Cancelar</a>
                        <?php endif ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col s12 m7">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Lista de Alunos</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>CPF</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alunos as $a): ?>
                                <tr>
                                    <td><?= h($a['nome']) ?></td>
                                    <td><?= h($a['email']) ?></td>
                                    <td><?= h($a['cpf'] ?: '-') ?></td>
                                    <td class="table-actions">
                                        <a class="btn-small waves-effect" href="alunos.php?edit=<?= h($a['id']) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <form method="post" class="inline-form" onsubmit="return confirm('Excluir este aluno?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= h($a['id']) ?>">
                                            <button class="btn-small red darken-1 waves-effect" type="submit">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($alunos) === 0): ?>
                                <tr>
                                    <td colspan="4">Nenhum aluno cadastrado.</td>
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


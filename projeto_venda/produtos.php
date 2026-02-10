<?php

require_once __DIR__ . '/bootstrap.php';

$model = new Produto();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $nome = trim($_POST['nome'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $preco = to_decimal($_POST['preco'] ?? 0);
            $estoque = (int) ($_POST['estoque'] ?? 0);
            $ativo = (int) ($_POST['ativo'] ?? 1);

            if ($nome === '' || $preco < 0) {
                flash_set('Preencha nome e preco valido.', 'warning');
                redirect('produtos.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($estoque < 0) {
                flash_set('Estoque nao pode ser negativo.', 'warning');
                redirect('produtos.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if (!in_array($ativo, [0, 1], true)) {
                flash_set('Status invalido.', 'warning');
                redirect('produtos.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($action === 'create') {
                $model->cadastrar($nome, $categoria, $preco, $estoque, $ativo, $descricao);
                flash_set('Produto cadastrado com sucesso.');
                redirect('produtos.php');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID invalido.', 'error');
                redirect('produtos.php');
            }

            $model->atualizar($id, $nome, $categoria, $preco, $estoque, $ativo, $descricao);
            flash_set('Produto atualizado com sucesso.');
            redirect('produtos.php');
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID invalido.', 'error');
                redirect('produtos.php');
            }

            $model->excluir($id);
            flash_set('Produto excluido.');
            redirect('produtos.php');
        }
    } catch (PDOException $e) {
        $isDuplicate = isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
        $msg = $isDuplicate ? 'Nome de produto ja cadastrado.' : 'Erro ao salvar no banco.';
        flash_set($msg, 'error');
        redirect('produtos.php');
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? $model->buscarPorId($editId) : null;
if ($editId > 0 && !$editing) {
    flash_set('Produto nao encontrado.', 'error');
    redirect('produtos.php');
}

$produtos = $model->listar();

$flash = flash_get();
$pageTitle = 'Produtos';
$pageSubtitle = 'Cadastro de produtos e controle de estoque.';
$active = 'produtos';
require __DIR__ . '/partials/header.php';
?>

<div class="row">
    <div class="col s12 m5">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title"><?= $editing ? 'Editar produto' : 'Cadastrar produto' ?></span>
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
                        <input id="categoria" name="categoria" type="text" value="<?= h($editing['categoria'] ?? '') ?>">
                        <label for="categoria" class="<?= ($editing && ($editing['categoria'] ?? '') !== '') ? 'active' : '' ?>">Categoria (opcional)</label>
                    </div>

                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="preco" name="preco" type="number" step="0.01" min="0" required value="<?= h($editing ? number_format((float) $editing['preco'], 2, '.', '') : '') ?>">
                            <label for="preco" class="active">Preco (R$)</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="estoque" name="estoque" type="number" min="0" required value="<?= h($editing['estoque'] ?? 0) ?>">
                            <label for="estoque" class="active">Estoque</label>
                        </div>
                    </div>

                    <div class="input-field">
                        <select name="ativo" required>
                            <option value="1" <?= (int) ($editing['ativo'] ?? 1) === 1 ? 'selected' : '' ?>>Ativo</option>
                            <option value="0" <?= (int) ($editing['ativo'] ?? 1) === 0 ? 'selected' : '' ?>>Inativo</option>
                        </select>
                        <label>Status</label>
                    </div>

                    <div class="input-field">
                        <textarea id="descricao" name="descricao" class="materialize-textarea"><?= h($editing['descricao'] ?? '') ?></textarea>
                        <label for="descricao" class="<?= ($editing && ($editing['descricao'] ?? '') !== '') ? 'active' : '' ?>">Descricao (opcional)</label>
                    </div>

                    <div class="form-actions">
                        <button class="btn waves-effect waves-light" type="submit">
                            <?= $editing ? 'Salvar' : 'Cadastrar' ?>
                        </button>
                        <?php if ($editing): ?>
                            <a class="btn-flat waves-effect" href="produtos.php">Cancelar</a>
                        <?php endif ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col s12 m7">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Lista de produtos</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Preco</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos as $p): ?>
                                <?php $estoqueBaixo = (int) $p['estoque'] <= 5; ?>
                                <tr>
                                    <td><?= h($p['nome']) ?></td>
                                    <td><?= h($p['categoria'] ?: '-') ?></td>
                                    <td>R$ <?= h(money($p['preco'])) ?></td>
                                    <td class="<?= $estoqueBaixo ? 'red-text text-darken-2' : '' ?>">
                                        <?= h($p['estoque']) ?>
                                    </td>
                                    <td>
                                        <span class="status-pill <?= (int) $p['ativo'] === 1 ? '' : 'is-inactive' ?>">
                                            <?= (int) $p['ativo'] === 1 ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td class="table-actions">
                                        <a class="btn-small waves-effect" href="produtos.php?edit=<?= h($p['id']) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <form method="post" class="inline-form" onsubmit="return confirm('Excluir este produto?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= h($p['id']) ?>">
                                            <button class="btn-small red darken-1 waves-effect" type="submit">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($produtos) === 0): ?>
                                <tr>
                                    <td colspan="6">Nenhum produto cadastrado.</td>
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

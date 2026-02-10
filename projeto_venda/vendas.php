<?php

require_once __DIR__ . '/bootstrap.php';

$model = new Venda();
$produtoModel = new Produto();

$produtos = $produtoModel->listar();

$formas = [
    '' => 'Sem forma',
    'DINHEIRO' => 'Dinheiro',
    'CARTAO' => 'Cartao',
    'PIX' => 'PIX',
    'BOLETO' => 'Boleto',
];

function validar_data($data)
{
    if ($data === null || $data === '') {
        return false;
    }

    $dt = DateTime::createFromFormat('Y-m-d', $data);
    return $dt && $dt->format('Y-m-d') === $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $produtoId = (int) ($_POST['produto_id'] ?? 0);
            $quantidade = (int) ($_POST['quantidade'] ?? 0);
            $precoUnitario = to_decimal($_POST['preco_unitario'] ?? 0);
            $desconto = to_decimal($_POST['desconto'] ?? 0);
            $dataVenda = trim($_POST['data_venda'] ?? '');
            $formaPagamento = trim($_POST['forma_pagamento'] ?? '');
            $observacao = trim($_POST['observacao'] ?? '');

            if ($produtoId <= 0 || $quantidade <= 0) {
                flash_set('Preencha produto e quantidade.', 'warning');
                redirect('vendas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if (!validar_data($dataVenda)) {
                flash_set('Data de venda invalida.', 'warning');
                redirect('vendas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($precoUnitario < 0 || $desconto < 0) {
                flash_set('Valores nao podem ser negativos.', 'warning');
                redirect('vendas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($formaPagamento !== '' && !isset($formas[$formaPagamento])) {
                flash_set('Forma de pagamento invalida.', 'warning');
                redirect('vendas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
            }

            if ($action === 'create') {
                $model->cadastrar($produtoId, $quantidade, $precoUnitario, $desconto, $dataVenda, $formaPagamento, $observacao);
                flash_set('Venda registrada com sucesso.');
                redirect('vendas.php');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID invalido.', 'error');
                redirect('vendas.php');
            }

            $model->atualizar($id, $produtoId, $quantidade, $precoUnitario, $desconto, $dataVenda, $formaPagamento, $observacao);
            flash_set('Venda atualizada com sucesso.');
            redirect('vendas.php');
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                flash_set('ID invalido.', 'error');
                redirect('vendas.php');
            }

            $model->excluir($id);
            flash_set('Venda excluida.');
            redirect('vendas.php');
        }
    } catch (RuntimeException $e) {
        flash_set($e->getMessage(), 'warning');
        redirect('vendas.php' . ($action === 'update' ? '?edit=' . (int) ($_POST['id'] ?? 0) : ''));
    } catch (PDOException $e) {
        flash_set('Erro ao salvar no banco.', 'error');
        redirect('vendas.php');
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? $model->buscarPorId($editId) : null;
if ($editId > 0 && !$editing) {
    flash_set('Venda nao encontrada.', 'error');
    redirect('vendas.php');
}

$vendas = $model->listar();

$flash = flash_get();
$pageTitle = 'Vendas';
$pageSubtitle = 'Registro de vendas e movimentacao de estoque.';
$active = 'vendas';
require __DIR__ . '/partials/header.php';
?>

<?php if (count($produtos) === 0): ?>
    <div class="card panel-card">
        <div class="card-content">
            <span class="card-title">Antes de registrar vendas</span>
            <p>Cadastre produtos em <a href="produtos.php">Produtos</a>.</p>
        </div>
    </div>
<?php endif ?>

<div class="row">
    <div class="col s12 m5">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title"><?= $editing ? 'Editar venda' : 'Registrar venda' ?></span>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?= h($editing['id']) ?>">
                    <?php endif ?>

                    <div class="input-field">
                        <select name="produto_id" id="produto_id" required>
                            <option value="" disabled <?= !$editing ? 'selected' : '' ?>>Selecione um produto</option>
                            <?php $selectedId = (int) ($editing['produto_id'] ?? 0); ?>
                            <?php foreach ($produtos as $p): ?>
                                <?php
                                $isSelected = $selectedId === (int) $p['id'];
                                $isInactive = (int) $p['ativo'] !== 1;
                                $disabled = $isInactive && !$isSelected ? 'disabled' : '';
                                $label = $p['nome'] . ($isInactive ? ' (inativo)' : '') . ' - Estoque: ' . $p['estoque'];
                                ?>
                                <option value="<?= h($p['id']) ?>" data-preco="<?= h(number_format((float) $p['preco'], 2, '.', '')) ?>" <?= $isSelected ? 'selected' : '' ?> <?= $disabled ?>>
                                    <?= h($label) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <label>Produto</label>
                    </div>

                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="quantidade" name="quantidade" type="number" min="1" required value="<?= h($editing['quantidade'] ?? 1) ?>">
                            <label for="quantidade" class="active">Quantidade</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="preco_unitario" name="preco_unitario" type="number" step="0.01" min="0" value="<?= h($editing ? number_format((float) $editing['preco_unitario'], 2, '.', '') : '') ?>">
                            <label for="preco_unitario" class="active">Preco unitario</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="desconto" name="desconto" type="number" step="0.01" min="0" value="<?= h($editing ? number_format((float) $editing['desconto'], 2, '.', '') : '0.00') ?>">
                            <label for="desconto" class="active">Desconto</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="data_venda" name="data_venda" type="date" required value="<?= h($editing['data_venda'] ?? date('Y-m-d')) ?>">
                            <label for="data_venda" class="active">Data da venda</label>
                        </div>
                    </div>

                    <div class="input-field">
                        <select name="forma_pagamento">
                            <?php foreach ($formas as $valor => $label): ?>
                                <?php $selected = (string) ($editing['forma_pagamento'] ?? '') === (string) $valor; ?>
                                <option value="<?= h($valor) ?>" <?= $selected ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach ?>
                        </select>
                        <label>Forma de pagamento</label>
                    </div>

                    <div class="input-field">
                        <textarea id="observacao" name="observacao" class="materialize-textarea"><?= h($editing['observacao'] ?? '') ?></textarea>
                        <label for="observacao" class="<?= ($editing && ($editing['observacao'] ?? '') !== '') ? 'active' : '' ?>">Observacao (opcional)</label>
                    </div>

                    <div class="muted-text total-preview">Total: <span id="total_preview">R$ 0,00</span></div>

                    <div class="form-actions">
                        <button class="btn waves-effect waves-light" type="submit" <?= count($produtos) === 0 ? 'disabled' : '' ?>>
                            <?= $editing ? 'Salvar' : 'Registrar' ?>
                        </button>
                        <?php if ($editing): ?>
                            <a class="btn-flat waves-effect" href="vendas.php">Cancelar</a>
                        <?php endif ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col s12 m7">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Lista de vendas</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Produto</th>
                                <th>Qtd</th>
                                <th>Total</th>
                                <th>Forma</th>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vendas as $v): ?>
                                <tr>
                                    <td><?= h(date('d/m/Y', strtotime($v['data_venda']))) ?></td>
                                    <td><?= h($v['produto_nome']) ?></td>
                                    <td><?= h($v['quantidade']) ?></td>
                                    <td>R$ <?= h(money($v['total'])) ?></td>
                                    <td><?= h($v['forma_pagamento'] ?: '-') ?></td>
                                    <td class="table-actions">
                                        <a class="btn-small waves-effect" href="vendas.php?edit=<?= h($v['id']) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <form method="post" class="inline-form" onsubmit="return confirm('Excluir esta venda?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= h($v['id']) ?>">
                                            <button class="btn-small red darken-1 waves-effect" type="submit">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($vendas) === 0): ?>
                                <tr>
                                    <td colspan="6">Nenhuma venda registrada.</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const produtoSelect = document.getElementById('produto_id');
        const precoInput = document.getElementById('preco_unitario');
        const qtdInput = document.getElementById('quantidade');
        const descontoInput = document.getElementById('desconto');
        const totalPreview = document.getElementById('total_preview');

        const parseValue = (value) => {
            const normalized = String(value || '').replace(',', '.');
            const parsed = parseFloat(normalized);
            return Number.isNaN(parsed) ? 0 : parsed;
        };

        const formatMoney = (value) => {
            return value.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };

        const updateTotal = () => {
            const quantidade = parseValue(qtdInput ? qtdInput.value : 0);
            const preco = parseValue(precoInput ? precoInput.value : 0);
            const desconto = parseValue(descontoInput ? descontoInput.value : 0);
            const total = Math.max(0, (quantidade * preco) - desconto);
            if (totalPreview) {
                totalPreview.textContent = 'R$ ' + formatMoney(total);
            }
        };

        if (produtoSelect && precoInput) {
            produtoSelect.addEventListener('change', (event) => {
                const option = event.target.selectedOptions[0];
                if (option && option.dataset.preco) {
                    precoInput.value = option.dataset.preco;
                }
                updateTotal();
            });
        }

        [precoInput, qtdInput, descontoInput].forEach((el) => {
            if (el) {
                el.addEventListener('input', updateTotal);
            }
        });

        updateTotal();
    });
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>

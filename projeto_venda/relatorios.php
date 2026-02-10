<?php

require_once __DIR__ . '/bootstrap.php';

$model = new Venda();

function validar_data($data)
{
    if ($data === null || $data === '') {
        return true;
    }

    $dt = DateTime::createFromFormat('Y-m-d', $data);
    return $dt && $dt->format('Y-m-d') === $data;
}

$inicio = trim($_GET['inicio'] ?? '');
$fim = trim($_GET['fim'] ?? '');

if (!validar_data($inicio)) {
    $inicio = '';
}

if (!validar_data($fim)) {
    $fim = '';
}

$inicioFiltro = $inicio !== '' ? $inicio : null;
$fimFiltro = $fim !== '' ? $fim : null;

$totalVendido = $model->totalVendido($inicioFiltro, $fimFiltro);
$totalItens = $model->totalItens($inicioFiltro, $fimFiltro);
$totalVendas = $model->contar($inicioFiltro, $fimFiltro);
$ticketMedio = $totalVendas > 0 ? $totalVendido / $totalVendas : 0;

$porProduto = $model->vendasPorProduto($inicioFiltro, $fimFiltro, 10);
$porForma = $model->vendasPorForma($inicioFiltro, $fimFiltro);

$flash = flash_get();
$pageTitle = 'Relatorios';
$pageSubtitle = 'Resumo simples com filtro por data.';
$active = 'relatorios';
require __DIR__ . '/partials/header.php';
?>

<div class="card panel-card">
    <div class="card-content">
        <span class="card-title">Filtros</span>
        <form method="get">
            <div class="row">
                <div class="input-field col s12 m4">
                    <input id="inicio" name="inicio" type="date" value="<?= h($inicio) ?>">
                    <label for="inicio" class="active">Data inicial</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="fim" name="fim" type="date" value="<?= h($fim) ?>">
                    <label for="fim" class="active">Data final</label>
                </div>
                <div class="input-field col s12 m4">
                    <button class="btn waves-effect waves-light" type="submit">Filtrar</button>
                    <a class="btn-flat waves-effect" href="relatorios.php">Limpar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col s12 m6 l3">
        <div class="card panel-card stat-card">
            <div class="card-content">
                <div class="stat-top">
                    <span class="stat-icon material-icons">attach_money</span>
                    <span class="stat-count">R$ <?= h(money($totalVendido)) ?></span>
                </div>
                <div class="stat-label">Total vendido</div>
            </div>
        </div>
    </div>

    <div class="col s12 m6 l3">
        <div class="card panel-card stat-card">
            <div class="card-content">
                <div class="stat-top">
                    <span class="stat-icon material-icons">shopping_cart</span>
                    <span class="stat-count"><?= h($totalItens) ?></span>
                </div>
                <div class="stat-label">Itens vendidos</div>
            </div>
        </div>
    </div>

    <div class="col s12 m6 l3">
        <div class="card panel-card stat-card">
            <div class="card-content">
                <div class="stat-top">
                    <span class="stat-icon material-icons">receipt_long</span>
                    <span class="stat-count"><?= h($totalVendas) ?></span>
                </div>
                <div class="stat-label">Numero de vendas</div>
            </div>
        </div>
    </div>

    <div class="col s12 m6 l3">
        <div class="card panel-card stat-card">
            <div class="card-content">
                <div class="stat-top">
                    <span class="stat-icon material-icons">insights</span>
                    <span class="stat-count">R$ <?= h(money($ticketMedio)) ?></span>
                </div>
                <div class="stat-label">Ticket medio</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col s12 m6">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Vendas por produto</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Qtd</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($porProduto as $p): ?>
                                <tr>
                                    <td><?= h($p['nome']) ?></td>
                                    <td><?= h($p['total_qtd']) ?></td>
                                    <td>R$ <?= h(money($p['total_vendido'])) ?></td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($porProduto) === 0): ?>
                                <tr>
                                    <td colspan="3">Sem dados para o periodo.</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col s12 m6">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Vendas por forma de pagamento</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Forma</th>
                                <th>Vendas</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($porForma as $f): ?>
                                <tr>
                                    <td><?= h($f['forma']) ?></td>
                                    <td><?= h($f['total_vendas']) ?></td>
                                    <td>R$ <?= h(money($f['total_valor'])) ?></td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($porForma) === 0): ?>
                                <tr>
                                    <td colspan="3">Sem dados para o periodo.</td>
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

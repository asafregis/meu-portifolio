<?php

require_once __DIR__ . '/bootstrap.php';

$produtoModel = new Produto();
$vendaModel = new Venda();

$totProdutos = $produtoModel->contar();
$totVendas = $vendaModel->contar();
$totalVendido = $vendaModel->totalVendido();
$estoqueBaixo = $produtoModel->contarEstoqueBaixo(5);

$ultimasVendas = $vendaModel->listarRecentes(6);
$topProdutos = $vendaModel->vendasPorProduto(null, null, 5);

$flash = flash_get();
$pageTitle = 'Dashboard';
$pageSubtitle = 'Visao geral do sistema de vendas.';
$active = 'dashboard';
require __DIR__ . '/partials/header.php';
?>

<div class="row">
    <div class="col s12 m6 l3">
        <div class="card panel-card stat-card">
            <div class="card-content">
                <div class="stat-top">
                    <span class="stat-icon material-icons">inventory_2</span>
                    <span class="stat-count"><?= h($totProdutos) ?></span>
                </div>
                <div class="stat-label">Produtos cadastrados</div>
            </div>
        </div>
    </div>

    <div class="col s12 m6 l3">
        <div class="card panel-card stat-card">
            <div class="card-content">
                <div class="stat-top">
                    <span class="stat-icon material-icons">point_of_sale</span>
                    <span class="stat-count"><?= h($totVendas) ?></span>
                </div>
                <div class="stat-label">Vendas registradas</div>
            </div>
        </div>
    </div>

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
                    <span class="stat-icon material-icons">warning</span>
                    <span class="stat-count"><?= h($estoqueBaixo) ?></span>
                </div>
                <div class="stat-label">Itens com estoque baixo</div>
                <div class="muted-text">Limite: ate 5 unidades</div>
            </div>
        </div>
    </div>
</div>

<div class="quick-actions">
    <a class="btn waves-effect waves-light" href="produtos.php">Cadastrar produto</a>
    <a class="btn waves-effect waves-light" href="vendas.php">Registrar venda</a>
    <a class="btn waves-effect waves-light" href="relatorios.php">Ver relatorios</a>
</div>

<div class="row">
    <div class="col s12 m6">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Ultimas vendas</span>
                <div class="responsive-table">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Produto</th>
                                <th>Qtd</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimasVendas as $v): ?>
                                <tr>
                                    <td><?= h(date('d/m/Y', strtotime($v['data_venda']))) ?></td>
                                    <td><?= h($v['produto_nome']) ?></td>
                                    <td><?= h($v['quantidade']) ?></td>
                                    <td>R$ <?= h(money($v['total'])) ?></td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($ultimasVendas) === 0): ?>
                                <tr>
                                    <td colspan="4">Nenhuma venda registrada.</td>
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
                <span class="card-title">Produtos em destaque</span>
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
                            <?php foreach ($topProdutos as $p): ?>
                                <tr>
                                    <td><?= h($p['nome']) ?></td>
                                    <td><?= h($p['total_qtd']) ?></td>
                                    <td>R$ <?= h(money($p['total_vendido'])) ?></td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (count($topProdutos) === 0): ?>
                                <tr>
                                    <td colspan="3">Sem dados de vendas.</td>
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

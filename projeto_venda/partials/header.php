<?php

$pageTitle = $pageTitle ?? 'Projeto Venda';
$pageSubtitle = $pageSubtitle ?? 'Sistema simples de vendas em PHP + MySQL';
$active = $active ?? 'dashboard';

$navItems = [
    [
        'key' => 'dashboard',
        'label' => 'Dashboard',
        'icon' => 'dashboard',
        'href' => 'index.php',
    ],
    [
        'key' => 'produtos',
        'label' => 'Produtos',
        'icon' => 'inventory_2',
        'href' => 'produtos.php',
    ],
    [
        'key' => 'vendas',
        'label' => 'Vendas',
        'icon' => 'point_of_sale',
        'href' => 'vendas.php',
    ],
    [
        'key' => 'relatorios',
        'label' => 'Relatorios',
        'icon' => 'assessment',
        'href' => 'relatorios.php',
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> | Projeto Venda</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Manrope:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <nav class="nav-extended app-nav">
        <div class="nav-wrapper container">
            <a href="index.php" class="brand-logo">Projeto Venda</a>
            <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>

            <ul class="right hide-on-med-and-down">
                <?php foreach ($navItems as $item): ?>
                    <li class="<?= $active === $item['key'] ? 'active' : '' ?>">
                        <a href="<?= h($item['href']) ?>">
                            <i class="material-icons left"><?= h($item['icon']) ?></i>
                            <?= h($item['label']) ?>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </nav>

    <ul class="sidenav" id="mobile-nav">
        <?php foreach ($navItems as $item): ?>
            <li class="<?= $active === $item['key'] ? 'active' : '' ?>">
                <a href="<?= h($item['href']) ?>">
                    <i class="material-icons"><?= h($item['icon']) ?></i>
                    <?= h($item['label']) ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>

    <header class="page-hero">
        <div class="container">
            <h1><?= h($pageTitle) ?></h1>
            <p><?= h($pageSubtitle) ?></p>
        </div>
    </header>

    <main class="container section">

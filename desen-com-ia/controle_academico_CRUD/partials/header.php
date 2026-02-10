<?php

$pageTitle = $pageTitle ?? 'Controle Acadêmico';
$pageSubtitle = $pageSubtitle ?? 'Sistema CRUD em PHP + MySQL';
$active = $active ?? 'dashboard';

$navItems = [
    [
        'key' => 'dashboard',
        'label' => 'Dashboard',
        'icon' => 'dashboard',
        'href' => 'index.php',
    ],
    [
        'key' => 'alunos',
        'label' => 'Alunos',
        'icon' => 'school',
        'href' => 'alunos.php',
    ],
    [
        'key' => 'professores',
        'label' => 'Professores',
        'icon' => 'person',
        'href' => 'professores.php',
    ],
    [
        'key' => 'cursos',
        'label' => 'Cursos',
        'icon' => 'book',
        'href' => 'cursos.php',
    ],
    [
        'key' => 'disciplinas',
        'label' => 'Disciplinas',
        'icon' => 'menu_book',
        'href' => 'disciplinas.php',
    ],
    [
        'key' => 'turmas',
        'label' => 'Turmas',
        'icon' => 'groups',
        'href' => 'turmas.php',
    ],
    [
        'key' => 'matriculas',
        'label' => 'Matrículas',
        'icon' => 'assignment',
        'href' => 'matriculas.php',
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> | Controle Acadêmico</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Space+Grotesk:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <nav class="nav-extended app-nav">
        <div class="nav-wrapper container">
            <a href="index.php" class="brand-logo">Controle</a>
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


<?php

require_once __DIR__ . '/bootstrap.php';

try {
    $alunos = new Aluno();
    $professores = new Professor();
    $cursos = new Curso();
    $disciplinas = new Disciplina();
    $turmas = new Turma();
    $matriculas = new Matricula();

    $stats = [
        [
            'label' => 'Alunos',
            'count' => $alunos->contar(),
            'href' => 'alunos.php',
            'icon' => 'school',
        ],
        [
            'label' => 'Professores',
            'count' => $professores->contar(),
            'href' => 'professores.php',
            'icon' => 'person',
        ],
        [
            'label' => 'Cursos',
            'count' => $cursos->contar(),
            'href' => 'cursos.php',
            'icon' => 'book',
        ],
        [
            'label' => 'Disciplinas',
            'count' => $disciplinas->contar(),
            'href' => 'disciplinas.php',
            'icon' => 'menu_book',
        ],
        [
            'label' => 'Turmas',
            'count' => $turmas->contar(),
            'href' => 'turmas.php',
            'icon' => 'groups',
        ],
        [
            'label' => 'Matrículas',
            'count' => $matriculas->contar(),
            'href' => 'matriculas.php',
            'icon' => 'assignment',
        ],
    ];
} catch (Throwable $e) {
    $stats = [];
    flash_set('Erro ao carregar o dashboard. Verifique o banco e o schema.sql.', 'error');
}

$flash = flash_get();
$pageTitle = 'Dashboard';
$pageSubtitle = 'Cadastre alunos, professores, cursos, disciplinas, turmas e matrículas.';
$active = 'dashboard';
require __DIR__ . '/partials/header.php';
?>

<div class="row">
    <?php foreach ($stats as $card): ?>
        <div class="col s12 m6 l4">
            <a class="card stat-card panel-card" href="<?= h($card['href']) ?>">
                <div class="card-content">
                    <div class="stat-top">
                        <i class="material-icons stat-icon"><?= h($card['icon']) ?></i>
                        <span class="stat-count"><?= h($card['count']) ?></span>
                    </div>
                    <span class="stat-label"><?= h($card['label']) ?></span>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<div class="row">
    <div class="col s12">
        <div class="card panel-card">
            <div class="card-content">
                <span class="card-title">Começar</span>
                <p class="muted-text">
                    Ordem recomendada: <strong>Cursos</strong> → <strong>Disciplinas</strong> → <strong>Professores</strong>
                    → <strong>Turmas</strong> → <strong>Alunos</strong> → <strong>Matrículas</strong>.
                </p>
                <div class="quick-actions">
                    <a class="btn waves-effect waves-light" href="cursos.php">Cadastrar curso</a>
                    <a class="btn-flat waves-effect" href="disciplinas.php">Cadastrar disciplina</a>
                    <a class="btn-flat waves-effect" href="turmas.php">Criar turma</a>
                    <a class="btn-flat waves-effect" href="alunos.php">Cadastrar aluno</a>
                    <a class="btn-flat waves-effect" href="matriculas.php">Matricular aluno</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>


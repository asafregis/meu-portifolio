<?php

require_once '../../classes/autoload.php';

$modalidade = trim((string) filter_input(INPUT_POST, 'modalidade', FILTER_SANITIZE_SPECIAL_CHARS));
$mensalidade = filter_input(INPUT_POST, 'mensalidade', FILTER_VALIDATE_FLOAT);

if ($modalidade === '' || $mensalidade === false) {
    header('Location: ../../public/modalidade.php?msg=Preencha+os+campos+corretamente.&type=error');
    exit;
}

$crud = new Modalidade();
$crud->dataDoForm($modalidade, $mensalidade);
$ok = $crud->create();

$msg = $ok ? 'Modalidade cadastrada com sucesso.' : 'Erro ao cadastrar modalidade.';
$type = $ok ? 'success' : 'error';
header('Location: ../../public/modalidade.php?msg=' . urlencode($msg) . '&type=' . $type);
exit;

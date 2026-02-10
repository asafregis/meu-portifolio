<?php

require_once '../../classes/autoload.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$modalidade = trim((string) filter_input(INPUT_POST, 'modalidade', FILTER_SANITIZE_SPECIAL_CHARS));
$mensalidade = filter_input(INPUT_POST, 'mensalidade', FILTER_VALIDATE_FLOAT);

if (!$id || $modalidade === '' || $mensalidade === false) {
    header('Location: ../../public/modalidade.php?msg=Dados+invalidos.&type=error');
    exit;
}

$crud = new Modalidade();
$ok = $crud->update($id, $modalidade, $mensalidade);

$msg = $ok ? 'Modalidade atualizada com sucesso.' : 'Erro ao atualizar modalidade.';
$type = $ok ? 'success' : 'error';
header('Location: ../../public/modalidade.php?msg=' . urlencode($msg) . '&type=' . $type);
exit;

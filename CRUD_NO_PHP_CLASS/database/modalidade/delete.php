<?php

require_once '../../classes/autoload.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: ../../public/modalidade.php?msg=ID+invalido.&type=error');
    exit;
}

$crud = new Modalidade();
$ok = $crud->delete($id);

$msg = $ok ? 'Modalidade excluida com sucesso.' : 'Erro ao excluir modalidade.';
$type = $ok ? 'success' : 'error';
header('Location: ../../public/modalidade.php?msg=' . urlencode($msg) . '&type=' . $type);
exit;

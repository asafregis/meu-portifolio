<?php

require_once '../../classes/autoload.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: ../../public/alunos.php?msg=ID+invalido.&type=error');
    exit;
}

$crud = new Alunos();
$ok = $crud->delete($id);

$msg = $ok ? 'Aluno excluido com sucesso.' : 'Erro ao excluir aluno.';
$type = $ok ? 'success' : 'error';
header('Location: ../../public/alunos.php?msg=' . urlencode($msg) . '&type=' . $type);
exit;

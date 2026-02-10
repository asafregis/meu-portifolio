<?php

require_once '../../classes/autoload.php';

$nome = trim((string) filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS));
$tel = trim((string) filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_SPECIAL_CHARS));
$email = trim((string) filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

if ($nome === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../public/alunos.php?msg=Preencha+nome+e+email+validos.&type=error');
    exit;
}

$crud = new Alunos();
$crud->dataDoForm($nome, $tel, $email);
$ok = $crud->create();

$msg = $ok ? 'Aluno cadastrado com sucesso.' : 'Erro ao cadastrar aluno.';
$type = $ok ? 'success' : 'error';
header('Location: ../../public/alunos.php?msg=' . urlencode($msg) . '&type=' . $type);
exit;

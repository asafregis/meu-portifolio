<?php

require_once '../../classes/autoload.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim((string) filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS));
$tel = trim((string) filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_SPECIAL_CHARS));
$email = trim((string) filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

if (!$id || $nome === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../public/alunos.php?msg=Dados+invalidos.&type=error');
    exit;
}

$crud = new Alunos();
$ok = $crud->update($id, $nome, $tel, $email);

$msg = $ok ? 'Aluno atualizado com sucesso.' : 'Erro ao atualizar aluno.';
$type = $ok ? 'success' : 'error';
header('Location: ../../public/alunos.php?msg=' . urlencode($msg) . '&type=' . $type);
exit;

<?php

require_once "../classes/autoload.php";

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$crud = new Alunos();
$lista = $crud->read();

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$registro = null;
$edit = false;

if ($editId) {
    $registro = $crud->find($editId);
    if ($registro) {
        $edit = true;
    }
}

$msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_SPECIAL_CHARS);
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS);
if ($editId && !$registro) {
    $msg = 'Aluno nao encontrado.';
    $type = 'error';
}

require_once "../config/header.inc.html";
?>

<div class="row container">
    <div class="col s12">
        <p>&nbsp;</p>
        <h5 class="light">Cadastro de alunos</h5>
        <hr>

        <?php if ($msg): ?>
            <?php $panel = ($type === 'error') ? 'red lighten-4 red-text text-darken-2' : 'green lighten-4 green-text text-darken-2'; ?>
            <div class="card-panel <?= $panel ?>"><?= h($msg) ?></div>
        <?php endif; ?>

        <?php
        $data = $registro ?: [];
        $edit = $edit;
        require_once '../forms/forms-add-aluno.php';
        ?>

        <p>&nbsp;</p>
        <h6 class="light">Lista de alunos</h6>
        <table class="striped highlight">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $item): ?>
                    <tr>
                        <td><?= h($item['id']) ?></td>
                        <td><?= h($item['nome']) ?></td>
                        <td><?= h($item['tel'] !== '' ? $item['tel'] : '-') ?></td>
                        <td><?= h($item['email']) ?></td>
                        <td>
                            <a class="btn-small amber darken-2" href="alunos.php?edit=<?= h($item['id']) ?>">
                                <i class="material-icons">edit</i>
                            </a>
                            <form action="../database/alunos/delete.php" method="post" style="display:inline-block" onsubmit="return confirm('Excluir este aluno?');">
                                <input type="hidden" name="id" value="<?= h($item['id']) ?>">
                                <button type="submit" class="btn-small red">
                                    <i class="material-icons">delete</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($lista) === 0): ?>
                    <tr>
                        <td colspan="5">Nenhum aluno cadastrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "../config/footer.inc.html"; ?>

<?php

require_once "../classes/autoload.php";

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$crud = new Modalidade();
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
    $msg = 'Modalidade nao encontrada.';
    $type = 'error';
}

require_once "../config/header.inc.html";
?>

<div class="row container">
    <div class="col s12">
        <p>&nbsp;</p>
        <h5 class="light">Cadastro de modalidades</h5>
        <hr>

        <?php if ($msg): ?>
            <?php $panel = ($type === 'error') ? 'red lighten-4 red-text text-darken-2' : 'green lighten-4 green-text text-darken-2'; ?>
            <div class="card-panel <?= $panel ?>"><?= h($msg) ?></div>
        <?php endif; ?>

        <?php
        $data = $registro ?: [];
        $edit = $edit;
        require_once '../forms/forms-add-mod.php';
        ?>

        <p>&nbsp;</p>
        <h6 class="light">Lista de modalidades</h6>
        <table class="striped highlight">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Modalidade</th>
                    <th>Mensalidade</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $item): ?>
                    <tr>
                        <td><?= h($item['id']) ?></td>
                        <td><?= h($item['modalidade']) ?></td>
                        <td>R$ <?= h(number_format((float) $item['mensalidade'], 2, ',', '.')) ?></td>
                        <td>
                            <a class="btn-small amber darken-2" href="modalidade.php?edit=<?= h($item['id']) ?>">
                                <i class="material-icons">edit</i>
                            </a>
                            <form action="../database/modalidade/delete.php" method="post" style="display:inline-block" onsubmit="return confirm('Excluir esta modalidade?');">
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
                        <td colspan="4">Nenhuma modalidade cadastrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "../config/footer.inc.html"; ?>

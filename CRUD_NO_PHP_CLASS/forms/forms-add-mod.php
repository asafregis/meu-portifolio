<?php
$edit = $edit ?? false;
$data = $data ?? [];
$action = $edit ? '../database/modalidade/update.php' : '../database/modalidade/create.php';
$modalidade = $data['modalidade'] ?? '';
$mensalidade = $data['mensalidade'] ?? '';
?>

<form action="<?= $action ?>" method="post" class="row">
    <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($data['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <div class="input-field col s12">
        <label for="modalidade" class="<?= $modalidade !== '' ? 'active' : '' ?>">Digite a modalidade:</label>
        <input type="text" name="modalidade" id="modalidade" maxlength="100" required autofocus value="<?= htmlspecialchars((string) $modalidade, ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="input-field col s12">
        <label for="mensalidade" class="<?= $mensalidade !== '' ? 'active' : '' ?>">Valor da mensalidade:</label>
        <input type="number" name="mensalidade" id="mensalidade" step="0.10" min="59.90" required value="<?= htmlspecialchars((string) $mensalidade, ENT_QUOTES, 'UTF-8') ?>">
    </div>
    
    <div class="input-field col s12">
        <input type="submit" value="<?= $edit ? 'Atualizar' : 'Cadastrar' ?>" class="btn">
        <input type="reset" value="Limpar" class="btn red">
        <?php if ($edit): ?>
            <a href="modalidade.php" class="btn grey">Cancelar</a>
        <?php endif; ?>
    </div>

</form>

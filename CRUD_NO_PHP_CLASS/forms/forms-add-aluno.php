<?php
$edit = $edit ?? false;
$data = $data ?? [];
$action = $edit ? '../database/alunos/update.php' : '../database/alunos/create.php';
$nome = $data['nome'] ?? '';
$tel = $data['tel'] ?? '';
$email = $data['email'] ?? '';
?>

<form action="<?= $action ?>" method="post" class="row">
    <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($data['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <div class="input-field col s12">
        <label for="nome" class="<?= $nome !== '' ? 'active' : '' ?>">Nome completo:</label>
        <input type="text" name="nome" id="nome" maxlength="120" required autofocus value="<?= htmlspecialchars((string) $nome, ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="input-field col s12">
        <label for="tel" class="<?= $tel !== '' ? 'active' : '' ?>">Telefone:</label>
        <input type="text" name="tel" id="tel" maxlength="20" value="<?= htmlspecialchars((string) $tel, ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="input-field col s12">
        <label for="email" class="<?= $email !== '' ? 'active' : '' ?>">Email:</label>
        <input type="email" name="email" id="email" maxlength="120" required value="<?= htmlspecialchars((string) $email, ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="input-field col s12">
        <input type="submit" value="<?= $edit ? 'Atualizar' : 'Cadastrar' ?>" class="btn">
        <input type="reset" value="Limpar" class="btn red">
        <?php if ($edit): ?>
            <a href="alunos.php" class="btn grey">Cancelar</a>
        <?php endif; ?>
    </div>
</form>

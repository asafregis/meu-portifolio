<?php require_once "../config/header.inc.html"; ?>

<div class="row container">
    <div class="col s12">
        <p>&nbsp;</p>
        <h5 class="light">Bem-vindo ao CRUD</h5>
        <p>Selecione uma das opcoes abaixo para gerenciar os dados.</p>
        <div class="row">
            <div class="col s12 m6">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Modalidades</span>
                        <p>Cadastro, edicao e exclusao de modalidades.</p>
                    </div>
                    <div class="card-action">
                        <a href="modalidade.php">Acessar</a>
                    </div>
                </div>
            </div>
            <div class="col s12 m6">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Alunos</span>
                        <p>Cadastro, edicao e exclusao de alunos.</p>
                    </div>
                    <div class="card-action">
                        <a href="alunos.php">Acessar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "../config/footer.inc.html"; ?>



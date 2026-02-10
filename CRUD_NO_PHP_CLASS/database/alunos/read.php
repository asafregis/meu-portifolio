<?php

require_once '../../classes/autoload.php';

$crud = new Alunos();
$alunos = $crud->read();

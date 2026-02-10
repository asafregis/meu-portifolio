<?php

require_once '../../classes/autoload.php';

$crud = new Modalidade();
$modalidades = $crud->read();

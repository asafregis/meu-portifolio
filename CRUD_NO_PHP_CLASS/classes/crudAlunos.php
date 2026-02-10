<?php
interface crudAlunos
{
    public function create();
    public function read();
    public function update($id, $nome, $tel, $email);
    public function delete ($id);
}


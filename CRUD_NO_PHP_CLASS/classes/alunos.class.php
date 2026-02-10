<?php

class Alunos extends Connection implements crudAlunos
{
    private $id;
    private $nome;
    private $tel;
    private $email;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getTel()
    {
        return $this->tel;
    }

    public function setTel($tel)
    {
        $this->tel = $tel;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function dataDoForm($nome, $tel, $email)
    {
        $this->setNome($nome);
        $this->setTel($tel);
        $this->setEmail($email);
    }

    public function create()
    {
        $sql = "INSERT INTO alunos (nome, tel, email) VALUES (?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([$this->getNome(), $this->getTel(), $this->getEmail()]);
    }

    public function read()
    {
        $sql = "SELECT * FROM alunos ORDER BY id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM alunos WHERE id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $data : null;
    }

    public function update($id, $nome, $tel, $email)
    {
        $sql = "UPDATE alunos SET nome = ?, tel = ?, email = ? WHERE id = ?";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([$nome, $tel, $email, $id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM alunos WHERE id = ?";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([$id]);
    }
}

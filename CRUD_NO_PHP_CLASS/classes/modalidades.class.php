<?php

class Modalidade extends Connection implements crudModalidades
{
    private $id;
    private $modalidade;
    private $mensalidade;

    //METODOS ESPECIAIS
    protected function getId()
    {
        return $this->id;
    }

    protected function setId($id)
    {
        $this->id = $id;
    }

    protected function getModalidade()
    {
        return $this->modalidade;
    }

    protected function setModalidade($modalidade)
    {
        $this->modalidade = $modalidade;
    }

    protected function getMensalidade()
    {
        return $this->mensalidade;
    }

    protected function setMensalidade($mensalidade)
    {
        $this->mensalidade = $mensalidade;
    }


    public function dataDoForm($modalidade, $mensalidade)
    {
        $this->setModalidade($modalidade);
        $this->setMensalidade($mensalidade);
    }

    public function create()
    {
        $sql = "INSERT INTO modalidade (modalidade, mensalidade) VALUES (?, ?)";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([$this->getModalidade(), $this->getMensalidade()]);
    }

    public function read()
    {
        $sql = "SELECT * FROM modalidade ORDER BY id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM modalidade WHERE id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $data : null;
    }

    public function update($id, $modalidade, $mensalidade)
    {
        $sql = "UPDATE modalidade SET modalidade = ?, mensalidade = ? WHERE id = ?";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([$modalidade, $mensalidade, $id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM modalidade WHERE id = ?";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([$id]);
    }
}

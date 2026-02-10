<?php

require_once __DIR__ . '/../config/Database.php';

class Produto
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }

    public function cadastrar($nome, $categoria, $preco, $estoque, $ativo, $descricao = null)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO produtos (nome, categoria, descricao, preco, estoque, ativo) VALUES (?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $nome,
            $categoria ?: null,
            $descricao ?: null,
            (float) $preco,
            (int) $estoque,
            (int) $ativo,
        ]);
    }

    public function atualizar($id, $nome, $categoria, $preco, $estoque, $ativo, $descricao = null)
    {
        $stmt = $this->conn->prepare(
            'UPDATE produtos SET nome=?, categoria=?, descricao=?, preco=?, estoque=?, ativo=? WHERE id=?'
        );

        return $stmt->execute([
            $nome,
            $categoria ?: null,
            $descricao ?: null,
            (float) $preco,
            (int) $estoque,
            (int) $ativo,
            (int) $id,
        ]);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT id, nome, categoria, descricao, preco, estoque, ativo FROM produtos WHERE id=?'
        );
        $stmt->execute([(int) $id]);
        $produto = $stmt->fetch();
        return $produto ?: null;
    }

    public function listar($apenasAtivos = false)
    {
        $sql = 'SELECT id, nome, categoria, descricao, preco, estoque, ativo FROM produtos';
        if ($apenasAtivos) {
            $sql .= ' WHERE ativo = 1';
        }
        $sql .= ' ORDER BY nome';

        return $this->conn->query($sql)->fetchAll();
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM produtos WHERE id=?');
        return $stmt->execute([(int) $id]);
    }

    public function contar()
    {
        return (int) $this->conn->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
    }

    public function contarEstoqueBaixo($limite = 5)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM produtos WHERE ativo = 1 AND estoque <= ?');
        $stmt->execute([(int) $limite]);
        return (int) $stmt->fetchColumn();
    }

    public function ajustarEstoque($id, $delta)
    {
        $stmt = $this->conn->prepare('UPDATE produtos SET estoque = estoque + ? WHERE id=?');
        return $stmt->execute([(int) $delta, (int) $id]);
    }
}

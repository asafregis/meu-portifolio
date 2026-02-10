<?php

require_once __DIR__ . '/../config/Database.php';

class Venda
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }

    private function buscarProduto($id)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, preco, estoque, ativo FROM produtos WHERE id=?');
        $stmt->execute([(int) $id]);
        $produto = $stmt->fetch();
        return $produto ?: null;
    }

    private function buildDateFilter($inicio, $fim, array &$params, $alias = 'v')
    {
        $conditions = [];

        if ($inicio) {
            $conditions[] = $alias . '.data_venda >= ?';
            $params[] = $inicio;
        }
        if ($fim) {
            $conditions[] = $alias . '.data_venda <= ?';
            $params[] = $fim;
        }

        return $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
    }

    public function cadastrar($produtoId, $quantidade, $precoUnitario, $desconto, $dataVenda, $formaPagamento, $observacao)
    {
        $this->conn->beginTransaction();
        try {
            $produto = $this->buscarProduto($produtoId);
            if (!$produto) {
                throw new RuntimeException('Produto nao encontrado.');
            }
            if ((int) $produto['ativo'] !== 1) {
                throw new RuntimeException('Produto inativo.');
            }
            if ($quantidade <= 0) {
                throw new RuntimeException('Quantidade invalida.');
            }

            $precoBase = (float) $produto['preco'];
            if ($precoUnitario <= 0) {
                $precoUnitario = $precoBase;
            }

            $desconto = max(0, (float) $desconto);
            $total = ($quantidade * $precoUnitario) - $desconto;
            if ($total < 0) {
                $total = 0;
            }

            $estoqueAtual = (int) $produto['estoque'];
            if ($estoqueAtual < $quantidade) {
                throw new RuntimeException('Estoque insuficiente.');
            }

            $stmt = $this->conn->prepare(
                'INSERT INTO vendas (produto_id, quantidade, preco_unitario, desconto, total, forma_pagamento, observacao, data_venda)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                (int) $produtoId,
                (int) $quantidade,
                (float) $precoUnitario,
                (float) $desconto,
                (float) $total,
                $formaPagamento ?: null,
                $observacao ?: null,
                $dataVenda,
            ]);

            $stmt = $this->conn->prepare('UPDATE produtos SET estoque = estoque - ? WHERE id=?');
            $stmt->execute([(int) $quantidade, (int) $produtoId]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function atualizar($id, $produtoId, $quantidade, $precoUnitario, $desconto, $dataVenda, $formaPagamento, $observacao)
    {
        $this->conn->beginTransaction();
        try {
            $vendaAtual = $this->buscarPorId($id);
            if (!$vendaAtual) {
                throw new RuntimeException('Venda nao encontrada.');
            }

            $produtoNovo = $this->buscarProduto($produtoId);
            if (!$produtoNovo) {
                throw new RuntimeException('Produto nao encontrado.');
            }
            if ((int) $produtoNovo['ativo'] !== 1) {
                throw new RuntimeException('Produto inativo.');
            }
            if ($quantidade <= 0) {
                throw new RuntimeException('Quantidade invalida.');
            }

            $precoBase = (float) $produtoNovo['preco'];
            if ($precoUnitario <= 0) {
                $precoUnitario = $precoBase;
            }

            $desconto = max(0, (float) $desconto);
            $total = ($quantidade * $precoUnitario) - $desconto;
            if ($total < 0) {
                $total = 0;
            }

            $produtoAntigoId = (int) $vendaAtual['produto_id'];
            $quantidadeAntiga = (int) $vendaAtual['quantidade'];

            if ($produtoAntigoId === (int) $produtoId) {
                $delta = $quantidade - $quantidadeAntiga;
                if ($delta > 0) {
                    $estoqueAtual = (int) $produtoNovo['estoque'];
                    if ($estoqueAtual < $delta) {
                        throw new RuntimeException('Estoque insuficiente.');
                    }
                }

                if ($delta !== 0) {
                    $stmt = $this->conn->prepare('UPDATE produtos SET estoque = estoque - ? WHERE id=?');
                    $stmt->execute([(int) $delta, (int) $produtoId]);
                }
            } else {
                $stmt = $this->conn->prepare('UPDATE produtos SET estoque = estoque + ? WHERE id=?');
                $stmt->execute([(int) $quantidadeAntiga, (int) $produtoAntigoId]);

                $estoqueNovo = (int) $produtoNovo['estoque'];
                if ($estoqueNovo < $quantidade) {
                    throw new RuntimeException('Estoque insuficiente.');
                }

                $stmt = $this->conn->prepare('UPDATE produtos SET estoque = estoque - ? WHERE id=?');
                $stmt->execute([(int) $quantidade, (int) $produtoId]);
            }

            $stmt = $this->conn->prepare(
                'UPDATE vendas SET produto_id=?, quantidade=?, preco_unitario=?, desconto=?, total=?, forma_pagamento=?, observacao=?, data_venda=? WHERE id=?'
            );
            $stmt->execute([
                (int) $produtoId,
                (int) $quantidade,
                (float) $precoUnitario,
                (float) $desconto,
                (float) $total,
                $formaPagamento ?: null,
                $observacao ?: null,
                $dataVenda,
                (int) $id,
            ]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function excluir($id)
    {
        $this->conn->beginTransaction();
        try {
            $vendaAtual = $this->buscarPorId($id);
            if (!$vendaAtual) {
                throw new RuntimeException('Venda nao encontrada.');
            }

            $stmt = $this->conn->prepare('DELETE FROM vendas WHERE id=?');
            $stmt->execute([(int) $id]);

            $stmt = $this->conn->prepare('UPDATE produtos SET estoque = estoque + ? WHERE id=?');
            $stmt->execute([(int) $vendaAtual['quantidade'], (int) $vendaAtual['produto_id']]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT id, produto_id, quantidade, preco_unitario, desconto, total, forma_pagamento, observacao, data_venda
             FROM vendas WHERE id=?'
        );
        $stmt->execute([(int) $id]);
        $venda = $stmt->fetch();
        return $venda ?: null;
    }

    public function listar()
    {
        return $this->conn->query(
            'SELECT v.id, v.produto_id, v.quantidade, v.preco_unitario, v.desconto, v.total, v.forma_pagamento, v.observacao, v.data_venda,
                    p.nome AS produto_nome, p.categoria AS produto_categoria
             FROM vendas v
             INNER JOIN produtos p ON p.id = v.produto_id
             ORDER BY v.data_venda DESC, v.id DESC'
        )->fetchAll();
    }

    public function listarRecentes($limite = 5)
    {
        $stmt = $this->conn->prepare(
            'SELECT v.id, v.produto_id, v.quantidade, v.total, v.data_venda,
                    p.nome AS produto_nome
             FROM vendas v
             INNER JOIN produtos p ON p.id = v.produto_id
             ORDER BY v.data_venda DESC, v.id DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, (int) $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contar($inicio = null, $fim = null)
    {
        $params = [];
        $where = $this->buildDateFilter($inicio, $fim, $params, 'v');
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM vendas v' . $where);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function totalVendido($inicio = null, $fim = null)
    {
        $params = [];
        $where = $this->buildDateFilter($inicio, $fim, $params, 'v');
        $stmt = $this->conn->prepare('SELECT COALESCE(SUM(v.total), 0) FROM vendas v' . $where);
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public function totalItens($inicio = null, $fim = null)
    {
        $params = [];
        $where = $this->buildDateFilter($inicio, $fim, $params, 'v');
        $stmt = $this->conn->prepare('SELECT COALESCE(SUM(v.quantidade), 0) FROM vendas v' . $where);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function vendasPorProduto($inicio = null, $fim = null, $limite = 0)
    {
        $params = [];
        $where = $this->buildDateFilter($inicio, $fim, $params, 'v');
        $sql = 'SELECT p.id, p.nome, SUM(v.quantidade) AS total_qtd, SUM(v.total) AS total_vendido
                FROM vendas v
                INNER JOIN produtos p ON p.id = v.produto_id' . $where .
                ' GROUP BY p.id, p.nome
                  ORDER BY total_vendido DESC';
        if ((int) $limite > 0) {
            $sql .= ' LIMIT ' . (int) $limite;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function vendasPorForma($inicio = null, $fim = null)
    {
        $params = [];
        $where = $this->buildDateFilter($inicio, $fim, $params, 'v');
        $sql = 'SELECT COALESCE(v.forma_pagamento, \'SEM FORMA\') AS forma, COUNT(*) AS total_vendas, SUM(v.total) AS total_valor
                FROM vendas v' . $where . '
                GROUP BY forma
                ORDER BY total_valor DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

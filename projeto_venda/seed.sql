USE projeto_venda;

START TRANSACTION;

INSERT INTO produtos (nome, categoria, descricao, preco, estoque, ativo) VALUES
('Camiseta Basica', 'Roupas', 'Algodao 100%', 39.90, 50, 1),
('Caneca Personalizada', 'Acessorios', 'Ceramica 300ml', 29.90, 40, 1),
('Caderno A5', 'Papelaria', '80 folhas', 18.50, 60, 1),
('Fone Bluetooth', 'Eletronicos', 'Bluetooth 5.0', 129.90, 25, 1),
('Garrafa Termica', 'Acessorios', '500ml inox', 59.90, 30, 1);

INSERT INTO vendas (produto_id, quantidade, preco_unitario, desconto, total, forma_pagamento, observacao, data_venda)
VALUES
((SELECT id FROM produtos WHERE nome = 'Camiseta Basica' LIMIT 1), 2, 39.90, 0.00, 79.80, 'PIX', 'Venda balc?o', '2026-02-01'),
((SELECT id FROM produtos WHERE nome = 'Caneca Personalizada' LIMIT 1), 1, 29.90, 0.00, 29.90, 'DINHEIRO', NULL, '2026-02-01'),
((SELECT id FROM produtos WHERE nome = 'Caderno A5' LIMIT 1), 3, 18.50, 5.00, 50.50, 'CARTAO', 'Desconto promocional', '2026-02-02'),
((SELECT id FROM produtos WHERE nome = 'Fone Bluetooth' LIMIT 1), 1, 129.90, 10.00, 119.90, 'PIX', NULL, '2026-02-03'),
((SELECT id FROM produtos WHERE nome = 'Garrafa Termica' LIMIT 1), 2, 59.90, 0.00, 119.80, 'CARTAO', NULL, '2026-02-03'),
((SELECT id FROM produtos WHERE nome = 'Camiseta Basica' LIMIT 1), 1, 39.90, 0.00, 39.90, 'DINHEIRO', NULL, '2026-02-04'),
((SELECT id FROM produtos WHERE nome = 'Caderno A5' LIMIT 1), 2, 18.50, 0.00, 37.00, 'PIX', NULL, '2026-02-05'),
((SELECT id FROM produtos WHERE nome = 'Caneca Personalizada' LIMIT 1), 2, 29.90, 0.00, 59.80, 'BOLETO', 'Pagamento em 2 dias', '2026-02-05');

UPDATE produtos p
SET p.estoque = p.estoque - (
    SELECT COALESCE(SUM(v.quantidade), 0)
    FROM vendas v
    WHERE v.produto_id = p.id
)
WHERE p.id IN (SELECT DISTINCT produto_id FROM vendas);

COMMIT;

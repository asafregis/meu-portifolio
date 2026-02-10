CREATE DATABASE IF NOT EXISTS projeto_venda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE projeto_venda;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    categoria VARCHAR(80) NULL,
    descricao TEXT NULL,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estoque INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_produtos_nome (nome)
);

CREATE TABLE vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    desconto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    forma_pagamento VARCHAR(30) NULL,
    observacao VARCHAR(255) NULL,
    data_venda DATE NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vendas_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_vendas_data (data_venda)
);

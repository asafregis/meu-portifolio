# Projeto Venda (CRUD) - PHP (POO) + MySQL

Projeto simples de sistema de vendas para:

- Cadastro de produtos
- Registro de vendas
- Total vendido
- Relatorios simples

## 1) Banco de dados

1. Abra o phpMyAdmin (XAMPP) e importe o arquivo `schema.sql`.
2. O script cria o banco `projeto_venda` e as tabelas com chaves e indices basicos.

## 2) Configuracao do PHP

Edite o arquivo `config/config.php` com usuario e senha do MySQL:

- `host`: normalmente `localhost`
- `name`: `projeto_venda`
- `user`: normalmente `root`
- `pass`: em XAMPP costuma ser vazio (ajuste se necessario)

## 3) Rodar no navegador

Abra no navegador (ajuste o caminho conforme seu projeto):

- `http://localhost/projeto_noite/projeto_venda/`

## Ordem recomendada de uso

1. Produtos
2. Vendas
3. Relatorios

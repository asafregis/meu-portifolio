# Controle Acadêmico (CRUD) — PHP (POO) + MySQL

Projeto simples de **controle acadêmico** para cadastrar e gerenciar:

- Alunos
- Professores
- Cursos
- Disciplinas
- Turmas
- Matrículas

## 1) Banco de dados

1. Abra o **phpMyAdmin** (XAMPP) e importe o arquivo `schema.sql`.
2. O script cria o banco `controle_academico` e as tabelas com **chaves primárias**, **chaves estrangeiras** e **restrições de unicidade**.

## 2) Configuração do PHP

Edite o arquivo `config/config.php` com o usuário/senha do seu MySQL:

- `host`: normalmente `localhost`
- `name`: `controle_academico`
- `user`: normalmente `root`
- `pass`: em XAMPP costuma ser vazio (`''`)

## 3) Rodar no navegador

Abra no navegador (ajuste o caminho conforme seu projeto):

- `http://localhost/projeto_noite/desen-com-ia/controle_academico_CRUD/`

## Ordem recomendada de cadastro

1. Cursos
2. Disciplinas
3. Professores
4. Turmas
5. Alunos
6. Matrículas


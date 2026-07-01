# Calculadora de Notas

Sistema web em PHP e MySQL para cadastro de usuarios, login e gerenciamento de alunos/notas. O projeto foi pensado para rodar em XAMPP e demonstrar autenticacao com sessoes, perfis de usuario e API simples em PHP.

O codigo principal esta em:

```text
calculadora de notas/
```

## Recursos

- Cadastro de usuarios.
- Login com sessao PHP.
- Perfis `admin` e `comum`.
- Cadastro e consulta de alunos.
- Usuario comum visualiza apenas seus proprios alunos.
- Usuario admin visualiza todos os alunos.
- API em PHP para login, cadastro, sessao, logout e alunos.
- Script SQL para criar o banco.

## Stack

- PHP 8+
- MySQL
- HTML5
- CSS3
- JavaScript
- XAMPP

## Estrutura

```text
calculadora de notas/
├── login.php
├── cadastro.php
├── indexnotas.php
├── admin.php
└── backend/
    ├── api/
    ├── config.php
    └── database.sql
```

## Como rodar no XAMPP

1. Copie a pasta `calculadora de notas` para `C:\xampp\htdocs\calculadora-notas`.
2. Inicie Apache e MySQL no XAMPP.
3. Abra `http://localhost/phpmyadmin`.
4. Importe `backend/database.sql`.
5. Acesse:

```text
http://localhost/calculadora-notas/login.php
```

## Endpoints

```text
POST /backend/api/cadastro.php
POST /backend/api/login.php
POST /backend/api/logout.php
GET  /backend/api/sessao.php
GET  /backend/api/alunos.php
POST /backend/api/alunos.php
```

## Seguranca aplicada

- Senhas com `password_hash`.
- Queries com prepared statements.
- Sessao com cookie `HttpOnly` e `SameSite=Strict`.
- Regeneracao de ID de sessao no login.
- Validacao de entrada no servidor.

## Status

Projeto academico funcional, com foco em backend PHP, banco de dados e controle de acesso simples.

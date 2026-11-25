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

Implementação acadêmica, com foco em backend PHP, banco de dados e controle de acesso simples.

## Modelo de dados, fluxo e limites

A interface chama `backend/api/`; os endpoints usam funções comuns de sessão/JSON e PDO. `usuarios` tem relação 1:N com `alunos` via `usuario_id`, com exclusão em cascata. A média aritmética de três notas é arredondada a duas casas: média ≥7 aprova, ≥5 fica em recuperação, abaixo de 5 reprova.

O cadastro aceita `cargo=admin` ou `comum` sem exigir administrador existente. É uma demonstração acadêmica que precisa restringir a criação de administradores antes de exposição pública. Não há proteção CSRF ou limite de tentativas de login implementados nesses endpoints.

### Configuração reproduzível

Requer PHP 8+, PDO MySQL, mbstring e MySQL/MariaDB. O [exemplo de variáveis](.env.example) é referência: o PHP lê o ambiente com `getenv` e **não carrega `.env` automaticamente**. Configure as cinco variáveis no processo/servidor PHP antes de iniciá-lo, usando o banco local `calculadora_notas`. No XAMPP, confira como o Apache recebe essas variáveis.

O SQL em `calculadora de notas/backend/database.sql` cria banco/tabelas. Importe manualmente apenas em instância local destinada ao projeto. Não há executor de migrations ou Composer versionado. Nenhuma importação foi executada nesta revisão.

Exemplo de leitura após login local: `GET /calculadora-notas/backend/api/alunos.php`, com cookie de sessão. O POST recebe `nome`, `nota1`, `nota2`, `nota3`. Não há endpoints de edição/exclusão de alunos nesse arquivo.

### Testes e evolução

Não há suíte automatizada ou CI versionados. A auditoria inspecionou código, sem login ou escrita em banco. Prioridades: testar isolamento entre perfis, notas limítrofes e restringir atribuição de administrador. Não há licença versionada.

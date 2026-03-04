# Calculadora de Notas com PHP (XAMPP)

Projeto com autenticação e registro de alunos usando:
- Front-end em HTML/CSS/JS
- Back-end em PHP 8+ (API simples)
- Banco MySQL (XAMPP)

## Recursos implementados
- Cadastro de usuário com senha segura (`password_hash`)
- Login com sessão PHP segura (`HttpOnly`, `SameSite=Strict`, regeneração de sessão)
- Controle de perfil (`admin` e `comum`)
- Cadastro de alunos com validação no servidor
- Consulta de alunos:
  - Usuário `comum`: vê apenas os próprios alunos
  - Usuário `admin`: vê todos os alunos
- Logout com destruição de sessão

## Estrutura
- `login.php` e `cadastro.php`: autenticação
- `indexnotas.php`: tela do usuário comum
- `admin.php`: painel administrativo
- `backend/api/*.php`: endpoints da API
- `backend/database.sql`: script de criação do banco

## Como rodar no XAMPP
1. Copie a pasta do projeto para `C:\xampp\htdocs\calculadora-notas`.
2. Abra o XAMPP Control Panel e inicie:
   - `Apache`
   - `MySQL`
3. Crie o banco:
   - Abra `http://localhost/phpmyadmin`
   - Vá em `Importar`
   - Importe o arquivo `backend/database.sql`
4. (Opcional) Se quiser outro nome de banco/usuário/senha, edite:
   - `backend/config.php`
5. Acesse:
   - `http://localhost/calculadora-notas/login.php`

## Endpoints da API
- `POST /backend/api/cadastro.php`
- `POST /backend/api/login.php`
- `POST /backend/api/logout.php`
- `GET  /backend/api/sessao.php`
- `GET  /backend/api/alunos.php`
- `POST /backend/api/alunos.php`

## Segurança aplicada (versão simples e profissional)
- Senha nunca é salva em texto puro (somente hash)
- Queries com prepared statements (proteção contra SQL Injection)
- Sessão com cookie `HttpOnly` e `SameSite=Strict`
- Regeneração do ID da sessão no login
- Validação de entrada no servidor (não confiar só no front-end)

## Observações para produção
- Em produção, use HTTPS para ativar cookie `Secure`
- Restrinja criação de usuários `admin` (hoje está liberado por simplicidade)
- Adicione proteção CSRF para ações sensíveis (cadastro/logout/alunos)
- Considere limitar tentativas de login (rate limit)

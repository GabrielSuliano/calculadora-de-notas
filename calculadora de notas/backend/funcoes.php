<?php
declare(strict_types=1);

function iniciarSessaoSegura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $config = require __DIR__ . '/config.php';
    $nomeSessao = $config['app']['session_name'];
    $usaHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_name($nomeSessao);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $usaHttps,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

function respostaJson(array $dados, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

function exigirMetodo(string $metodo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($metodo)) {
        respostaJson([
            'sucesso' => false,
            'mensagem' => 'Método não permitido.',
        ], 405);
    }
}

function obterDadosRequisicao(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
        $conteudo = file_get_contents('php://input');
        if ($conteudo === false || $conteudo === '') {
            return [];
        }

        $json = json_decode($conteudo, true);
        return is_array($json) ? $json : [];
    }

    return $_POST;
}

function limparTexto(?string $valor): string
{
    return trim((string) $valor);
}

function usuarioLogado(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function exigirLogin(): array
{
    $usuario = usuarioLogado();
    if (!$usuario) {
        respostaJson([
            'sucesso' => false,
            'mensagem' => 'Usuário não autenticado.',
        ], 401);
    }

    return $usuario;
}

function exigirAdmin(): array
{
    $usuario = exigirLogin();
    if (($usuario['cargo'] ?? '') !== 'admin') {
        respostaJson([
            'sucesso' => false,
            'mensagem' => 'Acesso permitido apenas para administrador.',
        ], 403);
    }

    return $usuario;
}


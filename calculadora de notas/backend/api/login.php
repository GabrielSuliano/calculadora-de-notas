<?php
declare(strict_types=1);

require_once __DIR__ . '/../funcoes.php';
require_once __DIR__ . '/../conexao.php';

iniciarSessaoSegura();
exigirMetodo('POST');

$dados = obterDadosRequisicao();
$usuario = limparTexto($dados['usuario'] ?? '');
$senha = (string) ($dados['senha'] ?? '');

if ($usuario === '' || $senha === '') {
    respostaJson([
        'sucesso' => false,
        'mensagem' => 'Usuário e senha são obrigatórios.',
    ], 422);
}

try {
    $pdo = obterPdo();
    $stmt = $pdo->prepare('SELECT id, usuario, senha_hash, cargo FROM usuarios WHERE usuario = :usuario LIMIT 1');
    $stmt->execute([':usuario' => $usuario]);
    $registro = $stmt->fetch();

    if (!$registro || !password_verify($senha, $registro['senha_hash'])) {
        respostaJson([
            'sucesso' => false,
            'mensagem' => 'Usuário ou senha inválidos.',
        ], 401);
    }

    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id' => (int) $registro['id'],
        'usuario' => $registro['usuario'],
        'cargo' => $registro['cargo'],
    ];

    respostaJson([
        'sucesso' => true,
        'mensagem' => 'Login realizado com sucesso.',
        'usuario' => $_SESSION['usuario'],
    ]);
} catch (PDOException $e) {
    respostaJson([
        'sucesso' => false,
        'mensagem' => 'Erro interno ao realizar login.',
    ], 500);
}


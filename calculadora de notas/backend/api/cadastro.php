<?php
declare(strict_types=1);

require_once __DIR__ . '/../funcoes.php';
require_once __DIR__ . '/../conexao.php';

iniciarSessaoSegura();
exigirMetodo('POST');

$dados = obterDadosRequisicao();
$usuario = limparTexto($dados['usuario'] ?? '');
$senha = (string) ($dados['senha'] ?? '');
$cargo = strtolower(limparTexto($dados['cargo'] ?? 'comum'));

if ($usuario === '' || $senha === '') {
    respostaJson([
        'sucesso' => false,
        'mensagem' => 'Usuário e senha são obrigatórios.',
    ], 422);
}

if (!preg_match('/^[a-zA-Z0-9._-]{3,30}$/', $usuario)) {
    respostaJson([
        'sucesso' => false,
        'mensagem' => 'Usuário deve ter entre 3 e 30 caracteres (letras, números, ponto, _ ou -).',
    ], 422);
}

if (mb_strlen($senha) < 6 || mb_strlen($senha) > 72) {
    respostaJson([
        'sucesso' => false,
        'mensagem' => 'A senha deve ter entre 6 e 72 caracteres.',
    ], 422);
}

if (mb_strtolower($senha) === mb_strtolower($usuario)) {
    respostaJson([
        'sucesso' => false,
        'mensagem' => 'A senha não pode ser igual ao usuário.',
    ], 422);
}

if (!in_array($cargo, ['admin', 'comum'], true)) {
    respostaJson([
        'sucesso' => false,
        'mensagem' => 'Cargo inválido.',
    ], 422);
}

try {
    $pdo = obterPdo();
    $hashSenha = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (usuario, senha_hash, cargo) VALUES (:usuario, :senha_hash, :cargo)'
    );
    $stmt->execute([
        ':usuario' => $usuario,
        ':senha_hash' => $hashSenha,
        ':cargo' => $cargo,
    ]);

    respostaJson([
        'sucesso' => true,
        'mensagem' => 'Usuário cadastrado com sucesso.',
    ], 201);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        respostaJson([
            'sucesso' => false,
            'mensagem' => 'Este usuário já existe.',
        ], 409);
    }

    respostaJson([
        'sucesso' => false,
        'mensagem' => 'Erro interno ao cadastrar usuário.',
    ], 500);
}


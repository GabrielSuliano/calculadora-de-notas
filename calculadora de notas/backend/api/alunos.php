<?php
declare(strict_types=1);

require_once __DIR__ . '/../funcoes.php';
require_once __DIR__ . '/../conexao.php';

iniciarSessaoSegura();
$usuario = exigirLogin();
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    listarAlunos($usuario);
}

if ($metodo === 'POST') {
    cadastrarAluno($usuario);
}

respostaJson([
    'sucesso' => false,
    'mensagem' => 'Método não permitido.',
], 405);

function listarAlunos(array $usuario): void
{
    $pdo = obterPdo();

    if (($usuario['cargo'] ?? '') === 'admin') {
        $sql = '
            SELECT a.id, a.nome, a.nota1, a.nota2, a.nota3, a.media, a.status, a.criado_em, u.usuario
            FROM alunos a
            INNER JOIN usuarios u ON u.id = a.usuario_id
            ORDER BY a.id DESC
        ';
        $stmt = $pdo->query($sql);
    } else {
        $sql = '
            SELECT a.id, a.nome, a.nota1, a.nota2, a.nota3, a.media, a.status, a.criado_em, u.usuario
            FROM alunos a
            INNER JOIN usuarios u ON u.id = a.usuario_id
            WHERE a.usuario_id = :usuario_id
            ORDER BY a.id DESC
        ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario_id' => (int) $usuario['id']]);
    }

    $alunos = $stmt->fetchAll();

    respostaJson([
        'sucesso' => true,
        'alunos' => $alunos,
    ]);
}

function cadastrarAluno(array $usuario): void
{
    $dados = obterDadosRequisicao();
    $nome = limparTexto($dados['nome'] ?? '');
    $nota1 = filter_var($dados['nota1'] ?? null, FILTER_VALIDATE_FLOAT);
    $nota2 = filter_var($dados['nota2'] ?? null, FILTER_VALIDATE_FLOAT);
    $nota3 = filter_var($dados['nota3'] ?? null, FILTER_VALIDATE_FLOAT);

    if ($nome === '' || $nota1 === false || $nota2 === false || $nota3 === false) {
        respostaJson([
            'sucesso' => false,
            'mensagem' => 'Preencha nome e notas corretamente.',
        ], 422);
    }

    if (
        $nota1 < 0 || $nota1 > 10 ||
        $nota2 < 0 || $nota2 > 10 ||
        $nota3 < 0 || $nota3 > 10
    ) {
        respostaJson([
            'sucesso' => false,
            'mensagem' => 'As notas devem estar entre 0 e 10.',
        ], 422);
    }

    if (mb_strlen($nome) > 120) {
        respostaJson([
            'sucesso' => false,
            'mensagem' => 'O nome do aluno deve ter no máximo 120 caracteres.',
        ], 422);
    }

    $media = round(($nota1 + $nota2 + $nota3) / 3, 2);
    $status = 'Reprovado';
    if ($media >= 7) {
        $status = 'Aprovado';
    } elseif ($media >= 5) {
        $status = 'Recuperação';
    }

    $pdo = obterPdo();
    $stmt = $pdo->prepare(
        'INSERT INTO alunos (usuario_id, nome, nota1, nota2, nota3, media, status)
         VALUES (:usuario_id, :nome, :nota1, :nota2, :nota3, :media, :status)'
    );
    $stmt->execute([
        ':usuario_id' => (int) $usuario['id'],
        ':nome' => $nome,
        ':nota1' => $nota1,
        ':nota2' => $nota2,
        ':nota3' => $nota3,
        ':media' => $media,
        ':status' => $status,
    ]);

    respostaJson([
        'sucesso' => true,
        'mensagem' => 'Aluno registrado com sucesso.',
        'aluno' => [
            'nome' => $nome,
            'nota1' => $nota1,
            'nota2' => $nota2,
            'nota3' => $nota3,
            'media' => $media,
            'status' => $status,
            'usuario' => $usuario['usuario'],
        ],
    ], 201);
}


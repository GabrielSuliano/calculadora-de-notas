<?php
declare(strict_types=1);

require_once __DIR__ . '/../funcoes.php';

iniciarSessaoSegura();
exigirMetodo('GET');

$usuario = usuarioLogado();

if (!$usuario) {
    respostaJson([
        'autenticado' => false,
    ]);
}

respostaJson([
    'autenticado' => true,
    'usuario' => $usuario,
]);


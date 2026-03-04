<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Escolar - Login</title>
<link rel="stylesheet" href="login.css">
</head>
<body>
<main class="pagina-autenticacao">
    <section class="cartao-autenticacao">
        <h1>Bem-vindo de volta</h1>
        <p class="subtitulo">Entre para acessar a calculadora de notas.</p>

        <label for="loginUsuario">Usuário</label>
        <input type="text" id="loginUsuario" placeholder="Digite seu usuário" autocomplete="username">

        <label for="loginSenha">Senha</label>
        <input type="password" id="loginSenha" placeholder="Digite sua senha" autocomplete="current-password">

        <button onclick="fazerLogin()">Entrar</button>

        <p id="mensagem" class="mensagem"></p>

        <p class="troca-pagina">
            Não tem conta?
            <a href="cadastro.php">Criar cadastro</a>
        </p>
    </section>
</main>

<script>
async function fazerLogin() {
    const usuario = document.getElementById("loginUsuario").value.trim();
    const senha = document.getElementById("loginSenha").value.trim();
    const mensagem = document.getElementById("mensagem");

    if (!usuario || !senha) {
        mensagem.innerText = "Preencha usuário e senha.";
        return;
    }

    mensagem.innerText = "Validando acesso...";

    try {
        const resposta = await fetch("backend/api/login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ usuario, senha })
        });

        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso) {
            mensagem.innerText = dados.mensagem || "Não foi possível fazer login.";
            return;
        }

        if (dados.usuario.cargo === "admin") {
            window.location.href = "admin.php";
            return;
        }

        window.location.href = "indexnotas.php";
    } catch (erro) {
        mensagem.innerText = "Erro de conexão com o servidor.";
    }
}

async function verificarSessaoInicial() {
    try {
        const resposta = await fetch("backend/api/sessao.php");
        const dados = await resposta.json();

        if (!resposta.ok || !dados.autenticado) {
            return;
        }

        if (dados.usuario.cargo === "admin") {
            window.location.href = "admin.php";
            return;
        }

        window.location.href = "indexnotas.php";
    } catch (erro) {
        // Se a API estiver fora do ar, mantém a tela de login.
    }
}

verificarSessaoInicial();
</script>
</body>
</html>

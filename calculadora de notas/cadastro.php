<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Escolar - Cadastro</title>
<link rel="stylesheet" href="cadastro.css">
</head>
<body>
<main class="pagina-autenticacao">
    <section class="cartao-autenticacao">
        <h1>Criar conta</h1>
        <p class="subtitulo">Cadastre um novo acesso para usar o sistema.</p>

        <label for="cadUsuario">Novo usuário</label>
        <input type="text" id="cadUsuario" placeholder="Digite um nome de usuário" autocomplete="username">

        <label for="cadSenha">Nova senha</label>
        <input type="password" id="cadSenha" placeholder="Crie uma senha" autocomplete="new-password">

        <label for="cadCargo">Cargo</label>
        <select id="cadCargo">
            <option value="comum" selected>comum</option>
            <option value="admin">admin</option>
        </select>

        <button onclick="cadastrar()">Cadastrar</button>

        <p id="mensagem" class="mensagem"></p>

        <p class="troca-pagina">
            Já tem conta?
            <a href="login.php">Voltar para login</a>
        </p>
    </section>
</main>

<script>
async function cadastrar() {
    const usuario = document.getElementById("cadUsuario").value.trim();
    const senha = document.getElementById("cadSenha").value.trim();
    const cargo = document.getElementById("cadCargo").value;
    const mensagem = document.getElementById("mensagem");

    if (!usuario || !senha || !cargo) {
        mensagem.innerText = "Preencha todos os campos.";
        return;
    }

    mensagem.innerText = "Criando conta...";

    try {
        const resposta = await fetch("backend/api/cadastro.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ usuario, senha, cargo })
        });

        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso) {
            mensagem.innerText = dados.mensagem || "Não foi possível cadastrar.";
            return;
        }

        mensagem.innerText = "Cadastro realizado com sucesso. Redirecionando para login...";
        setTimeout(() => {
            window.location.href = "login.php";
        }, 1200);
    } catch (erro) {
        mensagem.innerText = "Erro de conexão com o servidor.";
    }
}
</script>
</body>
</html>

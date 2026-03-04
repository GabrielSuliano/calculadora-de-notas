<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Admin</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Painel Administrativo</h1>

<button onclick="verAlunos()">Ver Alunos</button>
<button onclick="logout()">Sair</button>

<div id="lista"></div>

<script>
const lista = document.getElementById("lista");

async function requisicaoApi(url, opcoes = {}) {
    const resposta = await fetch(url, {
        ...opcoes,
        headers: {
            "Content-Type": "application/json",
            ...(opcoes.headers || {})
        }
    });

    const dados = await resposta.json();
    return { resposta, dados };
}

async function verificarAdmin() {
    try {
        const { resposta, dados } = await requisicaoApi("backend/api/sessao.php", { method: "GET" });

        if (!resposta.ok || !dados.autenticado) {
            window.location.href = "login.php";
            return;
        }

        if (dados.usuario.cargo !== "admin") {
            alert("Acesso negado.");
            window.location.href = "indexnotas.php";
        }
    } catch (erro) {
        alert("Não foi possível validar a sessão.");
        window.location.href = "login.php";
    }
}

async function verAlunos() {
    try {
        const { resposta, dados } = await requisicaoApi("backend/api/alunos.php", { method: "GET" });

        if (!resposta.ok || !dados.sucesso) {
            lista.innerHTML = "<p>Não foi possível carregar alunos.</p>";
            return;
        }

        if (dados.alunos.length === 0) {
            lista.innerHTML = "<p>Nenhum aluno cadastrado.</p>";
            return;
        }

        let html = "";

        dados.alunos.forEach((aluno) => {
            html += `<p>Usuário: ${aluno.usuario} | Nome: ${aluno.nome} | Média: ${Number(aluno.media).toFixed(2)} | Status: ${aluno.status}</p>`;
        });

        lista.innerHTML = html;
    } catch (erro) {
        lista.innerHTML = "<p>Erro de conexão com o servidor.</p>";
    }
}

async function logout() {
    try {
        await requisicaoApi("backend/api/logout.php", { method: "POST" });
    } catch (erro) {
        // Mesmo em erro, seguimos para login por segurança.
    }

    window.location.href = "login.php";
}

verificarAdmin();
</script>
</body>
</html>

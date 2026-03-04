<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Calculadora de Média</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2 id="usuarioLogado"></h2>
<h1>Calculadora de Média</h1>

<input type="number" id="nota1" placeholder="Nota 1" min="0" max="10" step="0.01">
<input type="number" id="nota2" placeholder="Nota 2" min="0" max="10" step="0.01">
<input type="number" id="nota3" placeholder="Nota 3" min="0" max="10" step="0.01">
<input type="text" id="nome" placeholder="Nome do aluno">
<button id="mostrar">Mostrar Alunos</button>
<button id="botao">Calcular Média</button>
<button onclick="logout()">Sair</button>

<p id="resultado"></p>

<script>
const campoResultado = document.getElementById("resultado");
const botaoCalcular = document.getElementById("botao");
const botaoMostrar = document.getElementById("mostrar");

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

async function verificarAcesso() {
    try {
        const { resposta, dados } = await requisicaoApi("backend/api/sessao.php", { method: "GET" });

        if (!resposta.ok || !dados.autenticado) {
            window.location.href = "login.php";
            return;
        }

        if (dados.usuario.cargo === "admin") {
            window.location.href = "admin.php";
            return;
        }

        document.getElementById("usuarioLogado").innerText = "Usuário: " + dados.usuario.usuario;
        atualizarResumoAlunos();
    } catch (erro) {
        alert("Não foi possível validar a sessão.");
        window.location.href = "login.php";
    }
}

async function calcularMedia() {
    const nota1 = parseFloat(document.getElementById("nota1").value);
    const nota2 = parseFloat(document.getElementById("nota2").value);
    const nota3 = parseFloat(document.getElementById("nota3").value);
    const nome = document.getElementById("nome").value.trim();

    if (!nome || Number.isNaN(nota1) || Number.isNaN(nota2) || Number.isNaN(nota3)) {
        campoResultado.innerText = "Preencha todos os campos.";
        return;
    }

    if (nota1 < 0 || nota1 > 10 || nota2 < 0 || nota2 > 10 || nota3 < 0 || nota3 > 10) {
        campoResultado.innerText = "As notas devem estar entre 0 e 10.";
        return;
    }

    try {
        const { resposta, dados } = await requisicaoApi("backend/api/alunos.php", {
            method: "POST",
            body: JSON.stringify({ nome, nota1, nota2, nota3 })
        });

        if (!resposta.ok || !dados.sucesso) {
            campoResultado.innerText = dados.mensagem || "Erro ao salvar aluno.";
            return;
        }

        const aluno = dados.aluno;
        campoResultado.innerText = `Aluno: ${aluno.nome} | Média: ${aluno.media.toFixed(2)} | Status: ${aluno.status}`;
        atualizarResumoAlunos();
    } catch (erro) {
        campoResultado.innerText = "Erro de conexão com o servidor.";
    }
}

async function atualizarResumoAlunos() {
    try {
        const { resposta, dados } = await requisicaoApi("backend/api/alunos.php", { method: "GET" });

        if (!resposta.ok || !dados.sucesso) {
            return;
        }

        if (dados.alunos.length === 0) {
            botaoMostrar.style.display = "none";
            campoResultado.innerText = "Nenhum aluno cadastrado.";
            return;
        }

        botaoMostrar.style.display = "inline-block";
        campoResultado.innerText = "Alunos cadastrados: " + dados.alunos.length;
    } catch (erro) {
        // Falha silenciosa para não interromper a tela principal.
    }
}

async function mostrarAlunos() {
    try {
        const { resposta, dados } = await requisicaoApi("backend/api/alunos.php", { method: "GET" });

        if (!resposta.ok || !dados.sucesso) {
            campoResultado.innerText = dados.mensagem || "Não foi possível carregar alunos.";
            return;
        }

        if (dados.alunos.length === 0) {
            campoResultado.innerText = "Nenhum aluno cadastrado.";
            return;
        }

        campoResultado.innerHTML = "";

        dados.alunos.forEach((aluno) => {
            const linha = document.createElement("div");
            linha.classList.add("aluno");
            linha.innerText = `Nome: ${aluno.nome} | Média: ${Number(aluno.media).toFixed(2)} | Status: ${aluno.status}`;
            campoResultado.appendChild(linha);
        });
    } catch (erro) {
        campoResultado.innerText = "Erro de conexão com o servidor.";
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

botaoCalcular.addEventListener("click", calcularMedia);
botaoMostrar.addEventListener("click", mostrarAlunos);
verificarAcesso();
</script>
</body>
</html>

<?php
// backend/php/cadastro.php

// Linhas para ajudar a depurar. Remova quando o site estiver em produção.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Inclui a conexão com o banco de dados
require 'conexao.php';

// 2. Verifica se o formulário foi de fato enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Se alguém tentar acessar o arquivo diretamente, volta para o formulário
    header('Location: ../../frontend/cadastro.html');
    exit;
}

// 3. Pega os dados enviados pelo formulário via POST
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$telefone = $_POST['telefone'] ?? '';

// 4. Validações (verifica se campos não estão vazios, etc.)
if (empty($nome) || empty($email) || empty($senha) || empty($telefone)) {
    header('Location: ../../frontend/cadastro.html?erro=campos_obrigatorios');
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../frontend/cadastro.html?erro=email_invalido');
    exit;
}

// 5. CRIA O HASH SEGURO DA SENHA (passo de segurança essencial)
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // 6. Verifica se o e-mail já existe
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: ../../frontend/cadastro.html?erro=email_existente');
        exit;
    }

    // 7. Insere o novo usuário no banco
    $stmt = $pdo->prepare(
        "INSERT INTO pacientes (nome, email, senha, telefone) VALUES (?, ?, ?, ?)"
    );
    
    // Executa a inserção com a SENHA SEGURA
    $stmt->execute([$nome, $email, $senha_hash, $telefone]);

    // 8. Se tudo deu certo, redireciona para a página de login com mensagem de sucesso
    header('Location: ../../frontend/login.html?sucesso=cadastro_realizado');
    exit;

} catch (PDOException $e) {
    // Se ocorrer um erro no banco, redireciona com uma mensagem genérica
    header('Location: ../../frontend/cadastro.html?erro=banco_de_dados');
    exit;
}
?>
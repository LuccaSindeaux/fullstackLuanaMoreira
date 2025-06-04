<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/cadastro.html');
    exit;
}

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$telefone = $_POST['telefone'] ?? '';

if (empty($nome) || empty($email) || empty($senha) || empty($telefone)) {
    header('Location: ../../frontend/cadastro.html?erro=campos_obrigatorios');
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../frontend/cadastro.html?erro=email_invalido');
    exit;
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: ../../frontend/cadastro.html?erro=email_existente');
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO pacientes (nome, email, senha, telefone) VALUES (?, ?, ?, ?)"
    );
    
    // Executa a inserção com a SENHA SEGURA
    $stmt->execute([$nome, $email, $senha_hash, $telefone]);

    // Se tudo deu certo, redireciona para a página de login com mensagem de sucesso
    header('Location: ../../frontend/login.html?sucesso=cadastro_realizado');
    exit;

} catch (PDOException $e) {
    // Se ocorrer um erro no banco, redireciona com uma mensagem genérica
    header('Location: ../../frontend/cadastro.html?erro=banco_de_dados');
    exit;
}
?>
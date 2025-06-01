<?php
require 'conexao.php';
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$confirmar = $_POST['confirmar'];
if ($senha !== $confirmar) {
  echo 'Senhas não coincidem.'; exit;
}
$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, is_admin) VALUES (?, ?, ?, 0)");
$ok = $stmt->execute([$nome, $email, $hash]);
header('Location: ../pagina/login.html');
?>
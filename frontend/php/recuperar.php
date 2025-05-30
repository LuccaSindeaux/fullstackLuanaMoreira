<?php
require 'conexao.php';
$email = $_POST['email'];
$nova = password_hash("123456", PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
$ok = $stmt->execute([$nova, $email]);
echo $ok ? "Nova senha definida como 123456" : "Erro ao redefinir senha.";
?>
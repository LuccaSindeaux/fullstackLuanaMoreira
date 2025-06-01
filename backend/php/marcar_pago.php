<?php
require 'conexao.php';
$data = json_decode(file_get_contents("php://input"), true);
$stmt = $pdo->prepare("UPDATE agendamentos SET pago = 1 WHERE id = ?");
$stmt->execute([$data['id']]);
?>
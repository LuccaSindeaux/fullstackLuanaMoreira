<?php
require 'conexao.php';
$stmt = $pdo->query("SELECT a.id, u.nome, a.data, a.plano, a.pago FROM agendamentos a JOIN usuarios u ON a.usuario_id = u.id");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
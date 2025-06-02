<?php
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['paciente_id'])) {
    http_response_code(403);
    exit;
}
$id = $_SESSION['paciente_id'];
$stmt = $pdo->prepare("SELECT nome, email FROM pacientes WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(['sucesso' => true, 'usuario' => $usuario]);
?>
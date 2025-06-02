<?php
require 'conexao.php'; 
header('Content-Type: application/json');

if (!isset($_SESSION['paciente_id'])) {
    http_response_code(401); 
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado.']);
    exit;
}

$id_paciente = $_SESSION['paciente_id'];
$params = []; 

$sql = "
    SELECT
        DATE(data_agendamento) as data,
        TIME(data_agendamento) as horario,
        status,
        plano,
        pago
    FROM agendamentos
    WHERE id_paciente = ?
";
$params[] = $id_paciente;

if (isset($_GET['data']) && !empty($_GET['data'])) {
    $sql .= " AND DATE(data_agendamento) = ?";
    $params[] = $_GET['data'];
}
$sql .= " ORDER BY data_agendamento DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($agendamentos);

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao buscar agendamentos.']);
}
?>
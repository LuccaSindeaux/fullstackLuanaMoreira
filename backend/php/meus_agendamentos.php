<?php
// backend/php/meus_agendamentos.php

require 'conexao.php'; // `conexao.php` já inicia a sessão, então session_start() aqui não é necessário.
header('Content-Type: application/json');

// 1. Corrigido para usar a variável de sessão que definimos no login.
//    No nosso código de verifica_login.php, usamos 'paciente_id'. Vamos manter o padrão.
if (!isset($_SESSION['paciente_id'])) {
    http_response_code(401); // Unauthorized - Código de erro HTTP mais apropriado
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado.']);
    exit;
}

$id_paciente = $_SESSION['paciente_id'];
$params = []; // Array para os parâmetros da query

// 2. A consulta SQL base foi melhorada para formatar data e hora separadamente.
//    E ajustada para usar 'id_paciente' como no resto do nosso código.
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

// 3. ADICIONADA A LÓGICA DE FILTRO POR DATA
//    Esta é a principal melhoria para fazer aquele último 'fetch' funcionar.
if (isset($_GET['data']) && !empty($_GET['data'])) {
    // Adiciona o filtro de data na consulta SQL se ele for passado na URL
    $sql .= " AND DATE(data_agendamento) = ?";
    $params[] = $_GET['data'];
}

// Adiciona ordenação para mostrar os mais recentes primeiro
$sql .= " ORDER BY data_agendamento DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params); // Executa com os parâmetros (seja só o ID ou ID + data)
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($agendamentos);

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    // Não exponha a mensagem de erro detalhada em produção por segurança
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao buscar agendamentos.']);
}
?>
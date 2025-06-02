<?php
require 'conexao.php'; 
header('Content-Type: application/json'); 

// O 'is_admin' é definido na sessão durante o login.
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

try {
    $sql = "SELECT id, data_hora, status FROM disponibilidade ORDER BY data_hora ASC";

    // Se o usuário NÃO for um administrador, a condicional busca apenas horários com status 'disponivel'.
    if (!$is_admin) {
        $sql = "SELECT id, data_hora, status FROM disponibilidade WHERE status = 'disponivel' ORDER BY data_hora ASC";
    }

    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $eventos = [];

    foreach ($results as $row) {
        // O JavaScript no dashboard.html e agenda.html usará o 'status'
        $eventos[] = [
            'id'    => $row['id'],
            'start' => $row['data_hora'], 
            'allDay' => false, 
            'extendedProps' => [
                'status' => $row['status']
            ]
        ];
    }
    
    echo json_encode($eventos);

} catch (PDOException $e) {
    http_response_code(500); // Erro no Servidor
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao carregar os horários.']);
}
?>
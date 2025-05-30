<?php
header( 'Content-Type: application/json' );
require 'conexao.php';
session_start();

if ( !isset( $_SESSION[ 'usuario_id' ] ) ) {
    echo json_encode( [ 'erro' => 'Usuário não autenticado' ] );
    exit;
}

$id_usuario = $_SESSION[ 'usuario_id' ];

try {
    $stmt = $pdo->prepare( 'SELECT data, horario, status, plano, pago FROM agendamentos WHERE usuario_id = ?' );

    $stmt->execute( [ $id_usuario ] );
    $agendamentos = $stmt->fetchAll( PDO::FETCH_ASSOC );

    echo json_encode( $agendamentos );
} catch ( PDOException $e ) {
    echo json_encode( [ 'erro' => 'Erro ao buscar agendamentos: ' . $e->getMessage() ] );
}
?>
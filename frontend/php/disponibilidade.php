<?php
require 'conexao.php';
header( 'Content-Type: application/json' );

$sql = 'SELECT id, data, horario, status FROM disponibilidades';
$result = $conn->query( $sql );

$eventos = [];

while ( $row = $result->fetch_assoc() ) {
    $start = $row[ 'data' ] . 'T' . $row[ 'horario' ];
    $cor = $row[ 'status' ] === 'disponivel' ? '#27ae60' : '#7f8c8d';

    $eventos[] = [
        'id' => $row[ 'id' ],
        'title' => ucfirst( $row[ 'status' ] ),
        'start' => $start,
        'allDay' => false,
        'backgroundColor' => $cor,
        'borderColor' => $cor,
        'status' => $row[ 'status' ],
    ];
}

echo json_encode( $eventos );
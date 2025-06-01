<?php
require 'conexao.php';

$method = $_SERVER[ 'REQUEST_METHOD' ];
$data = json_decode( file_get_contents( 'php://input' ), true );

if ( $method === 'POST' ) {
    // Criar novo evento
    $dataDia = $data[ 'data' ];
    $horario = $data[ 'horario' ];
    $status = $data[ 'status' ] ?? 'disponivel';

    $stmt = $conn->prepare( 'INSERT INTO disponibilidades (data, horario, status) VALUES (?, ?, ?)' );
    $stmt->bind_param( 'sss', $dataDia, $horario, $status );
    if ( $stmt->execute() ) {
        echo 'Disponibilidade salva com sucesso.';
    } else {
        http_response_code( 500 );
        echo 'Erro ao salvar.';
    }
} elseif ( $method === 'PUT' ) {
    // Editar evento existente
    $id = $data[ 'id' ];
    $dataDia = $data[ 'data' ];
    $horario = $data[ 'horario' ];
    $status = $data[ 'status' ];

    $stmt = $conn->prepare( 'UPDATE disponibilidades SET data = ?, horario = ?, status = ? WHERE id = ?' );
    $stmt->bind_param( 'sssi', $dataDia, $horario, $status, $id );
    if ( $stmt->execute() ) {
        echo 'Disponibilidade atualizada com sucesso.';
    } else {
        http_response_code( 500 );
        echo 'Erro ao atualizar.';
    }
} elseif ( $method === 'DELETE' ) {
    // Excluir evento
    $id = $data[ 'id' ];

    $stmt = $conn->prepare( 'DELETE FROM disponibilidades WHERE id = ?' );
    $stmt->bind_param( 'i', $id );
    if ( $stmt->execute() ) {
        echo 'Disponibilidade excluída com sucesso.';
    } else {
        http_response_code( 500 );
        echo 'Erro ao excluir.';
    }
} else {
    http_response_code( 405 );
    echo 'Método não permitido.';
}
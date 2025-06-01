<?php
require 'conexao.php';

$dados = json_decode( file_get_contents( 'php://input' ), true );
$data = $dados[ 'data' ] ?? null;
$hora = $dados[ 'hora' ] ?? null;

if ( !$data || !$hora ) {
    http_response_code( 400 );
    echo 'Dados incompletos.';
    exit;
}

try {
    $stmt = $pdo->prepare( 'INSERT INTO disponibilidades (data, horario) VALUES (?, ?)' );
    $stmt->execute( [ $data, $hora ] );
    echo 'Disponibilidade salva.';
} catch ( Exception $e ) {
    http_response_code( 500 );
    echo 'Erro: ' . $e->getMessage();
}
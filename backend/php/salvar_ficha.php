<?php
session_start();
require 'conexao.php';

if ( !isset( $_SESSION[ 'usuario_id' ] ) ) {
    echo "<script>alert('Você precisa estar logado para agendar.'); window.location.href='../index.html';</script>";
    exit;
}

$data = $_POST;
$usuario_id = $_SESSION[ 'usuario_id' ];
$data_agendamento = $data[ 'data_agendamento' ] ?? null;

try {
    // Inserir ficha
    $stmt = $pdo->prepare( "INSERT INTO fichas (
        nome, idade, estado_civil, email, nascimento, telefone,
        praticou_yoga, coluna, cirurgias, atividade_fisica, qual_atividade, plano
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );

    $stmt->execute( [
        $data[ 'nome' ], $data[ 'idade' ], $data[ 'estado_civil' ], $data[ 'email' ], $data[ 'nascimento' ],
        $data[ 'telefone' ], $data[ 'praticou_yoga' ], $data[ 'coluna' ], $data[ 'cirurgias' ],
        $data[ 'atividade_fisica' ], $data[ 'qual_atividade' ], $data[ 'plano' ]
    ] );

    // Agendar, se data preenchida
    if ( $data_agendamento && $horario ) {
        $plano = $data[ 'plano' ] ?? null;

        $stmt2 = $pdo->prepare( 'INSERT INTO agendamentos (usuario_id, data, horario, status, plano, pago) VALUES (?, ?, ?, ?, ?, 0)' );
        $stmt2->execute( [ $usuario_id, $data_agendamento, $horario, $status, $plano ] );
    }

    echo "<script>alert('Ficha preenchida e agendada com sucesso!'); window.location.href='../paginas/agenda.html';</script>";
} catch ( Exception $e ) {
    echo "<script>alert('Erro ao salvar ficha: " . $e->getMessage() . "'); window.history.back();</script>";
}
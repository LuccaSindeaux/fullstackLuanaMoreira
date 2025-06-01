<?php
session_start();
header( 'Content-Type: application/json' );

// Verificação de sessão via GET
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'GET' ) {
    if ( isset( $_SESSION[ 'usuario_id' ] ) ) {
        echo json_encode( [
            'logado' => true,
            'nome' => $_SESSION[ 'usuario_nome' ],
            'admin' => $_SESSION[ 'is_admin' ] == 1
        ] );
    } else {
        echo json_encode( [ 'logado' => false ] );
    }
    exit;
}

// Se for POST, é tentativa de login
$host = 'localhost';
$db = 'fisio';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO( "mysql:host=$host;dbname=$db;charset=$charset", $user, $pass );
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch ( PDOException $e ) {
    echo json_encode( [ 'sucesso' => false, 'mensagem' => 'Erro de conexão com o banco.' ] );
    exit;
}

$email = $_POST[ 'email' ] ?? '';
$senha = $_POST[ 'senha' ] ?? '';

$stmt = $pdo->prepare( 'SELECT * FROM usuarios WHERE email = ?' );
$stmt->execute( [ $email ] );
$usuario = $stmt->fetch( PDO::FETCH_ASSOC );

if ( $usuario ) {
    $senha_hash = hash( 'sha256', $senha );

    if ( $senha_hash === $usuario[ 'senha' ] ) {
        $_SESSION[ 'usuario_id' ] = $usuario[ 'id' ];
        $_SESSION[ 'usuario_nome' ] = $usuario[ 'nome' ];
        $_SESSION[ 'is_admin' ] = $usuario[ 'is_admin' ];

        echo json_encode( [
            'sucesso' => true,
            'nome' => $usuario[ 'nome' ],
            'admin' => $usuario[ 'is_admin' ] == 1
        ] );
        exit;
    }
}

echo json_encode( [
    'sucesso' => false,
    'mensagem' => 'E-mail ou senha incorretos.'
] );
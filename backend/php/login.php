<?php
session_start();
header( 'Content-Type: application/json' );

// Conexão com o banco
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

// Coleta os dados do formulário
$email = $_POST[ 'email' ] ?? '';
$senha = $_POST[ 'senha' ] ?? '';

// Busca o usuário pelo e-mail
$stmt = $pdo->prepare( 'SELECT * FROM usuarios WHERE email = ?' );
$stmt->execute( [ $email ] );
$usuario = $stmt->fetch( PDO::FETCH_ASSOC );

if ( $usuario ) {
    // As senhas estão com hash SHA256 ( sem `password_hash` ), então comparamos diretamente
    $senha_hash = hash( 'sha256', $senha );

    if ( $senha_hash === $usuario[ 'senha' ] ) {
        // Login válido
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

// Login inválido
echo json_encode( [
    'sucesso' => false,
    'mensagem' => 'E-mail ou senha incorretos.'
] );
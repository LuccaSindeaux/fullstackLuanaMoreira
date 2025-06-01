<?php
// backend/conexao.php

// --- CONTROLE DE ACESSO (CORS) ---
// Permite que o seu frontend (ex: http://localhost ou o seu domínio final) acesse este backend.
// Substitua '*' por 'http://seu-dominio-do-frontend.com' em produção para mais segurança.
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Permite que a sessão (login) funcione entre domínios diferentes.
header("Access-Control-Allow-Credentials: true");

// O navegador envia uma requisição OPTIONS "pre-flight" para verificar as permissões.
// Se for uma requisição OPTIONS, apenas retornamos os cabeçalhos acima e encerramos.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- CONEXÃO COM O BANCO DE DADOS ---
$host = 'localhost';
$db   = 'luana_moreira_fisioterapia';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}

// Inicia a sessão para controle de login.
if (session_status() === PHP_SESSION_NONE) {
    // Configurações do cookie de sessão para funcionar com CORS
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => '', // Deixe em branco para localhost, defina seu domínio em produção
        'secure' => isset($_SERVER['HTTPS']), // true em produção
        'httponly' => true,
        'samesite' => 'None' // Essencial para CORS com credenciais
    ]);
    session_start();
}
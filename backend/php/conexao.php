<?php
// backend/php/conexao.php

// --- CONTROLE DE ACESSO (CORS) ---
// Agora que frontend e backend devem estar em 'localhost',
// o CORS se torna menos crítico, mas vamos manter para consistência.
header("Access-Control-Allow-Origin: http://localhost"); // Permite requisições de http://localhost
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); 
header("Access-Control-Allow-Credentials: true");

// Trata as requisições OPTIONS "pre-flight"
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Se o navegador enviar um Access-Control-Request-Method, responda com os métodos permitidos.
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        // Reafirma os métodos (não estritamente necessário se já definido acima, mas bom para clareza)
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    }
    // Se o navegador enviar um Access-Control-Request-Headers, responda com os cabeçalhos permitidos.
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    http_response_code(200);
    exit(); // Encerra o script para requisições OPTIONS
}

// --- GERENCIAMENTO DE SESSÃO ---
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400, 
        'path' => '/',
        // 'domain' => '', // Deixar vazio é geralmente melhor para localhost
        'secure' => false, // Forçando false para HTTP local, já que não estamos em HTTPS
        'httponly' => true, 
        'samesite' => 'Lax' // 'Lax' é um bom padrão para a maioria dos casos
    ]);
    session_start();
}

// --- CONEXÃO COM O BANCO DE DADOS ---
$host = 'localhost';
$db   = 'luana_moreira_fisioterapia'; // Garanta que este é o nome correto do seu banco
$user = 'root';
$pass = ''; // Sua senha do MySQL, se houver
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
    // Para depuração: error_log("Erro de conexão PDO: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}
?>
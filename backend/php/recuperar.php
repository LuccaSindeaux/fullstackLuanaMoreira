<?php
// backend/php/recuperar.php

require 'conexao.php';

// --- ATENÇÃO: Para envio de e-mail real, use uma biblioteca como PHPMailer ---
// require 'vendor/autoload.php';
// use PHPMailer\PHPMailer\PHPMailer;

// Valida o método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/index.html');
    exit;
}

$email = $_POST['email'] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Se o email for inválido, redireciona de volta
    header('Location: ../../frontend/paginas/recuperar.html?status=email_invalido');
    exit;
}

try {
    // 1. Verifica se o e-mail existe na tabela `pacientes` (e não `usuarios`)
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE email = ?");
    $stmt->execute([$email]);

    // 2. Se o e-mail existir, inicia o processo de recuperação
    if ($stmt->fetch()) {
        // Gera um token secreto e aleatório
        $token = bin2hex(random_bytes(32));
        
        // Define a data de expiração do token (ex: 1 hora)
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Salva o token na tabela `password_resets` para validação futura
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires_at]);

        // 3. Monta o link e envia o e-mail (aqui é a parte teórica)
        $linkDeRecuperacao = "http://localhost/fullstackLuanaMoreira/frontend/paginas/redefinir_senha.html?token=" . $token;
        
        $assunto = "Recuperação de Senha - Luana Moreira Fisioterapia";
        $corpo = "Olá!\n\nVocê solicitou a redefinição de sua senha. Se foi você, clique no link a seguir para criar uma nova senha. O link é válido por 1 hora:\n\n" . $linkDeRecuperacao . "\n\nSe não foi você, por favor, ignore este e-mail.\n\nAtenciosamente,\nEquipe Luana Moreira Fisioterapia";
        $headers = "From: nao-responda@luanamoreira.com";
        
        // A função mail() do PHP não é confiável para produção.
        // O ideal é configurar uma biblioteca como PHPMailer.
        // mail($email, $assunto, $corpo, $headers);
    }

    // 4. IMPORTANTE: Sempre redirecione para a página de sucesso,
    // mesmo que o e-mail não exista. Isso impede que pessoas mal-intencionadas
    // descubram quais e-mails estão cadastrados no seu sistema.
    header('Location: ../../frontend/paginas/recuperar.html?status=sucesso');
    exit;

} catch (PDOException $e) {
    // Em caso de erro de banco de dados, redireciona para uma página de erro genérica
    // Não mostre o erro real para o usuário por segurança.
    // error_log($e->getMessage()); // Salva o erro real em um log no servidor
    header('Location: ../../frontend/paginas/recuperar.html?status=erro_servidor');
    exit;
}
?>
ATENÇÃO, NÃO ESQUECER PARAA VERSÃO FINAL DA APLICAÇÃO:

No arquivo recuperar.php, os comentários sobre a biblioteca "PHPMailer" devem ser descomentados quando esta biblioteca for instalada. Motivo: segundo pesquisas, esta biblioteca é necessária para que no momento de recuperar senha o e-mail enviado não caia n caixa de SPAM. A instalção deve ser feita seguindo os comando no site oficial: https://getcomposer.org/download 

Para além disto, na hora de usar a aplicação com o composer instalado:  Configurar uma Conta de E-mail para Envio (SMTP)
É necessário uma  conta de e-mail para enviar as mensagens. Para questões de segurança é importante que a verificação em duas etapas do e-mail que a Luana usar esteja ativa.

Abaixo está como ficaria o arquivo com PHPMailer:

require '../vendor/autoload.php'; // Carrega o PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$linkDeRecuperacao = "http://localhost/fullstackLuanaMoreira/frontend/paginas/redefinir_senha.html?token=" . $token;
$mail = new PHPMailer(true);

try {
    //Configurações do servidor SMTP (usando Gmail como exemplo)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'seu_email@gmail.com'; // SEU EMAIL GMAIL
    $mail->Password   = 'sua_senha_de_app_de_16_letras'; // SUA SENHA DE APP GERADA
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    //Quem envia e quem recebe
    $mail->setFrom('seu_email@gmail.com', 'Luana Moreira Fisioterapia');
    $mail->addAddress($email); // O e-mail do usuário que pediu a recuperação

    //Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Recuperação de Senha - Luana Moreira Fisioterapia';
    $mail->Body    = "Olá!<br><br>Você solicitou a redefinição de sua senha. Se foi você, clique no link a seguir para criar uma nova senha. O link é válido por 1 hora:<br><br><a href='$linkDeRecuperacao'>Redefinir Minha Senha</a><br><br>Se não foi você, por favor, ignore este e-mail.<br><br>Atenciosamente,<br>Equipe Luana Moreira Fisioterapia";
    $mail->AltBody = "Olá! Você solicitou a redefinição de sua senha. Copie e cole este link no seu navegador para continuar: " . $linkDeRecuperacao;

    $mail->send();
    // Se chegou aqui, o email foi enviado com sucesso.

} catch (Exception $e) {
    // Se deu erro no envio, podemos registrar o erro sem quebrar a aplicação para o usuário
    // error_log("Mailer Error: {$mail->ErrorInfo}");
}
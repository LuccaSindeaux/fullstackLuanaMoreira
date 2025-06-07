<h3>1. Sobre a ideia do projeto e sua conclusão:</h3>

Um site de serviços de agendamentos para uma fisioterapeuta. A ideia é que exdistam dois tipos de login, identificados pela parte de nosso PHP que diferencia entre perfil de administrador e paciente. O site possui alguma melhorias que devem ser feitas, o prazo para sua realização foi de cinco semanas, porém como a equipe era composta inteiramente de estudantes cursando outras cadeiras, a deidcação ao projeto não era exclusiva. 

<h3>2. Frontend - parte gráfica:</h3>

O site foi esboçado na ferramenta Figma como é de costume por uma equipe de trÊs alunos da PMM do SenacRS, o GitHub de uma das alunas desta equipe está listado como contribuidor deste projeto. 

<h3>3. Frontend - código:</h3>

A equipe que fez os códigos de frontend também era um trio. Criando a separação por pastas. Na raix da macropasta frontend está index, que irá acessar os outros arquivos html na pasta "paginas", a estilização na pasta css, e a configuração na pasta javaScript. 

O arquivo config.js é o principal para o funcionamento do site. Apesar de possir um única linha, ela é a declaração dda constante que chama a API do backend, que por sua vez faz a conexão com o banco de dados. A constante é chamada diversas vezes no arquivo index.js, que possui as funções que aplicam as funcionalidades do php no nosso site.

<h3>4. Sobre a construção do calendário:</h3>

A metodologia fullCalendar foi usada, usando a linha de código  "const calendar = new FullCalendar.Calendar()".
A ideia era fazer algo similiar ao Google calendar, onde com um toque o paciente conseguiria marcar que deseja pegar a consulta de um dia x num horário y.
Nossa cliente contudo queria ela mesma definir os horários disponíveis, e os paicnetes ó poderiam escolher os horários que ela mesma. Com seus eventos e atualizações sendo exibidos com "calendar.render();".

<h4>Erros atuais e funcionalidades que podem ser implementadas</h4>
I - Uma pessoa sem login que entra n página de agenda (agenda.html) não consegue ver os horários que já foram pegos, ou seja, a página atualiza os horários indisponívies somente para pacientes e administradores logados.
II - Ficou incerto se a cliente desejava que vários pacientes marcassem um único horário (digamos que ela escolhesse que um dteerminado somente 3 pacientes poderiam escolher aquele horário); portanto nosso código prevê somente o agendamento de <strong>um cliente por horário disponível</strong>.
III - Na página do calendário, no seu final, há a funcionalidade de mostrar as fichas dos pacientes que agendaram um horário, estas por algum motivo estão se duplicando ou mosytrando informações de múltipllos pacientes.
IV - Da construção da ficha: Por algum motivo, toda vez que o paciente vai agendar ele tem de preencher a ficha com seu nome e e-mail, o ideal é que estes dados fossem preenchidos automaticamente com o usuário logado. eele deveria completar somente a parte de cirirgias e atividsade física; também deveria ser possível para ele editar esta ficha (na ocasionalidade dele começar uma nova rotina de exercícios, relaizar uma cirurgia, ou mudar de plano de pagamento).
V - Implementar a possibilidade de cancelar uma aula marcada devido a imprevistos, e a fioterapeuta ser prontamente notificada disto.



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
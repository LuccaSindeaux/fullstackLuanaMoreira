<?php
require 'conexao.php'; 
header('Content-Type: application/json');

// 1. VERIFICA SE O PACIENTE ESTÁ LOGADO (usando nosso padrão 'paciente_id')
if (!isset($_SESSION['paciente_id'])) {
    http_response_code(401); 
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você precisa estar logado para agendar.']);
    exit;
}
$id_paciente = $_SESSION['paciente_id'];

// 2. PEGA OS DADOS JSON ENVIADOS PELO JAVASCRIPT
$dados = json_decode(file_get_contents('php://input'), true);

if (!$dados) {
    http_response_code(400); 
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum dado recebido.']);
    exit;
}

// 3. ATRIBUI OS DADOS A VARIÁVEIS
$data_agendamento = $dados['data_agendamento'] ?? null;
$plano = $dados['plano'] ?? null;

try {
    // 4. INSERE OS DADOS NA TABELA 'fichas'
    $stmtFicha = $pdo->prepare(
        "INSERT INTO fichas (
            id_paciente, nome, idade, estado_civil, email, nascimento, telefone,
            praticou_yoga, coluna, cirurgias, atividade_fisica, qual_atividade, plano
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmtFicha->execute([
        $id_paciente, $dados['nome'], $dados['idade'], $dados['estado_civil'], $dados['email'], 
        $dados['nascimento'], $dados['telefone'], $dados['praticou_yoga'], $dados['coluna'], 
        $dados['cirurgias'], $dados['atividade_fisica'], $dados['qual_atividade'] ?? null, $plano
    ]);

    // 5. CRIA O AGENDAMENTO FINAL (se uma data foi fornecida)
    if ($data_agendamento) {
        // Encontra o ID do horário na tabela de disponibilidade
        $stmtHorario = $pdo->prepare("SELECT id FROM disponibilidade WHERE data_hora = ? AND status = 'disponivel'");
        $stmtHorario->execute([$data_agendamento]);
        $horario = $stmtHorario->fetch();

        if (!$horario) {
            http_response_code(409); // Conflict
            echo json_encode(['sucesso' => false, 'mensagem' => 'Desculpe, este horário foi agendado por outra pessoa enquanto você preenchia a ficha. Por favor, escolha outro.']);
            exit;
        }
        $id_disponibilidade = $horario['id'];

        // Insere o agendamento
        $stmtAgendamento = $pdo->prepare(
            "INSERT INTO agendamentos (id_paciente, id_disponibilidade, data_agendamento, plano, status) VALUES (?, ?, ?, ?, 'Confirmado')"
        );
        $stmtAgendamento->execute([$id_paciente, $id_disponibilidade, $data_agendamento, $plano]);

        // Marca o horário como indisponível para outros
        $stmtUpdate = $pdo->prepare("UPDATE disponibilidade SET status = 'indisponivel' WHERE id = ?");
        $stmtUpdate->execute([$id_disponibilidade]);
    }

    // 6. ENVIA A RESPOSTA DE SUCESSO EM FORMATO JSON
    echo json_encode(['sucesso' => true, 'mensagem' => 'Ficha e agendamento enviados com sucesso!']);

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ocorreu um erro no servidor. Tente novamente.']);
    // Para depuração: error_log($e->getMessage());
}
?>
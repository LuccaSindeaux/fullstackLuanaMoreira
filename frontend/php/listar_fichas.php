<?php
require 'conexao.php';
$stmt = $pdo->query("SELECT nome, idade, email FROM fichas");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
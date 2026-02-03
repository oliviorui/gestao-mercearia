<?php
require_once '../config/database.php';
session_start();  // Inicia a sessão

header('Content-Type: application/json'); 

// Verificar se o usuário está logado (por exemplo, verificando uma variável de sessão)
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']['id_usuario'])) {
    echo json_encode(['error' => 'Usuário não está logado.']);
    exit;  // Encerra a execução da API se o usuário não estiver logado
}

// Verifica se o usuário logado é do tipo 'admin'
if ($_SESSION['usuario']['tipo_usuario'] !== 'admin') {
    echo json_encode(['error' => 'Acesso restrito. Somente administradores podem acessar esses dados.']);
    exit;  // Encerra a execução se o usuário não for admin
}

try {
    $database = new Database();
    $conn = $database->conectar();

    // Consulta SQL para obter todas as vendas
    $sql_vendas = "SELECT v.id_venda, v.data_venda, v.valor_total, u.nome as usuario 
    FROM vendas v
    JOIN usuarios u ON v.id_usuario = u.id_usuario";
    $stmt_vendas = $conn->prepare($sql_vendas);
    $stmt_vendas->execute();

    // Recuperar todas as vendas
    $vendas = $stmt_vendas->fetchAll(PDO::FETCH_ASSOC);

    // Verificar se há vendas
    if (count($vendas) > 0) {
        echo json_encode($vendas);
    } else {
        echo json_encode(['message' => 'Nenhuma venda encontrada']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao obter as vendas: ' . $e->getMessage()]);
}
?>

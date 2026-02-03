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

    // Consulta SQL para obter todos os logs
    $sql = "SELECT l.id_log, l.data_hora, l.tipo_actividade, l.descricao, u.nome as usuario
            FROM logs l
            JOIN usuarios u ON l.id_usuario = u.id_usuario
            ORDER BY l.data_hora DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Recuperar todos os logs
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar se há logs
    if (count($logs) > 0) {
        echo json_encode($logs);  // Retornar os logs em formato JSON
    } else {
        echo json_encode(['message' => 'Nenhum log encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao obter logs: ' . $e->getMessage()]);
}
?>

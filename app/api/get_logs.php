<?php
require_once __DIR__ . '/../helpers/security.php';
require_api_auth('admin');

try {
    $database = new Database();
    $conn = $database->conectar();

    if (!$conn) {
        throw new Exception('Não foi possível conectar à base de dados.');
    }

    $sql = "SELECT l.id_log, l.data_hora, l.tipo_actividade, l.descricao, COALESCE(u.nome, 'Usuário removido') AS usuario
            FROM logs l
            LEFT JOIN usuarios u ON l.id_usuario = u.id_usuario
            ORDER BY l.data_hora DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao obter logs.']);
    error_log('Erro ao obter logs: ' . $e->getMessage());
}
?>

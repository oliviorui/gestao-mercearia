<?php
require_once __DIR__ . '/../helpers/security.php';
require_api_auth('admin');

try {
    $database = new Database();
    $conn = $database->conectar();

    if (!$conn) {
        throw new Exception('Não foi possível conectar à base de dados.');
    }

    $sql = "SELECT v.id_venda, v.data_venda, v.valor_total, u.nome AS usuario
            FROM vendas v
            LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
            ORDER BY v.data_venda DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao obter as vendas.']);
    error_log('Erro ao obter vendas: ' . $e->getMessage());
}
?>

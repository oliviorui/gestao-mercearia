<?php
require_once __DIR__ . '/../helpers/security.php';
require_api_auth('admin');

try {
    $database = new Database();
    $conn = $database->conectar();

    if (!$conn) {
        throw new Exception('Não foi possível conectar à base de dados.');
    }

    $stmt = $conn->prepare("SELECT id_usuario, nome, email, tipo_usuario, data_cadastro FROM usuarios ORDER BY nome ASC");
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao obter usuários.']);
    error_log('Erro ao obter usuários: ' . $e->getMessage());
}
?>

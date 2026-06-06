<?php
require_once __DIR__ . '/../helpers/security.php';
require_api_auth(['admin', 'operador']);

try {
    $database = new Database();
    $conn = $database->conectar();

    if (!$conn) {
        throw new Exception('Não foi possível conectar à base de dados.');
    }

    $searchTerm = trim($_GET['search'] ?? '');
    $sql = "SELECT id_produto, nome, categoria, preco, quantidade_estoque, descricao
            FROM produtos
            WHERE nome LIKE :searchNome OR CAST(id_produto AS CHAR) LIKE :searchId
            ORDER BY nome ASC";
    $stmt = $conn->prepare($sql);
    $like = '%' . $searchTerm . '%';
    $stmt->execute([
        ':searchNome' => $like,
        ':searchId' => $like,
    ]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao obter produtos.']);
    error_log('Erro ao obter produtos: ' . $e->getMessage());
}
?>

<?php
require_once '../config/database.php';
session_start();  // Inicia a sessão

header('Content-Type: application/json');

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Verificar se o usuário está logado (por exemplo, verificando uma variável de sessão)
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']['id_usuario'])) {
    echo json_encode(['error' => 'Usuário não está logado.']);
    exit;  // Encerra a execução da API se o usuário não estiver logado
}

try {
    $database = new Database();
    $conn = $database->conectar();

    // Consulta SQL com filtro
    $sql_produtos = "SELECT id_produto, nome, categoria, preco, quantidade_estoque, descricao 
                     FROM produtos 
                     WHERE nome LIKE :searchTerm OR id_produto LIKE :searchTerm";
    $stmt_produtos = $conn->prepare($sql_produtos);
    $stmt_produtos->bindValue(':searchTerm', '%' . $searchTerm . '%');
    $stmt_produtos->execute();

    // Recuperar todos os produtos
    $produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

    // Verificar se há produtos
    if (count($produtos) > 0) {
        echo json_encode($produtos);
    } else {
        echo json_encode(['message' => 'Nenhum produto encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao obter produtos: ' . $e->getMessage()]);
}
?>

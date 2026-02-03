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

    // Consultar o total de vendas
    $sql_total_vendas = "SELECT COUNT(id_venda) AS total_vendas FROM vendas";
    $stmt_total_vendas = $conn->prepare($sql_total_vendas);
    $stmt_total_vendas->execute();
    $total_vendas = $stmt_total_vendas->fetch(PDO::FETCH_ASSOC)['total_vendas'];

    // Consultar o número de produtos no estoque (quantidade_estoque > 0)
    $sql_produtos_estoque = "SELECT COUNT(id_produto) AS produtos_estoque FROM produtos WHERE quantidade_estoque > 10";
    $stmt_produtos_estoque = $conn->prepare($sql_produtos_estoque);
    $stmt_produtos_estoque->execute();
    $produtos_estoque = $stmt_produtos_estoque->fetch(PDO::FETCH_ASSOC)['produtos_estoque'];

    // Consultar o número de produtos fora do estoque (quantidade_estoque = 0)
    $sql_produtos_fora_estoque = "SELECT COUNT(id_produto) AS produtos_fora_estoque FROM produtos WHERE quantidade_estoque <= 10";
    $stmt_produtos_fora_estoque = $conn->prepare($sql_produtos_fora_estoque);
    $stmt_produtos_fora_estoque->execute();
    $produtos_fora_estoque = $stmt_produtos_fora_estoque->fetch(PDO::FETCH_ASSOC)['produtos_fora_estoque'];

    // Retornar os dados em formato JSON
    echo json_encode([
        'total_vendas' => $total_vendas,
        'produtos_estoque' => $produtos_estoque,
        'produtos_fora_estoque' => $produtos_fora_estoque
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao buscar dados do painel: ' . $e->getMessage()]);
}
?>

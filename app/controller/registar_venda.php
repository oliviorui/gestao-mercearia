<?php
require_once __DIR__ . '/../helpers/security.php';
require_auth(['admin', 'operador'], '../views/login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/operador/vendas.php");
    exit();
}

require_valid_csrf();

$database = new Database();
$conn = $database->conectar();

try {
    if (!$conn) {
        throw new Exception('Não foi possível conectar à base de dados.');
    }

    $produtos = $_POST['produto'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];

    if (!is_array($produtos) || !is_array($quantidades) || count($produtos) === 0 || count($produtos) !== count($quantidades)) {
        throw new Exception('Dados da venda inválidos.');
    }

    $id_usuario = (int)$_SESSION['usuario']['id_usuario'];
    $valor_total = 0;
    $itens = [];

    $conn->beginTransaction();

    foreach ($produtos as $i => $id_produto) {
        $id_produto = (int)$id_produto;
        $quantidade = (int)($quantidades[$i] ?? 0);

        if ($id_produto <= 0 || $quantidade <= 0) {
            throw new Exception('Produto ou quantidade inválida.');
        }

        $stmtProduto = $conn->prepare("SELECT id_produto, nome, preco, quantidade_estoque FROM produtos WHERE id_produto = :id_produto FOR UPDATE");
        $stmtProduto->execute([':id_produto' => $id_produto]);
        $produto = $stmtProduto->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            throw new Exception('Produto não encontrado.');
        }

        if ((int)$produto['quantidade_estoque'] < $quantidade) {
            throw new Exception('Estoque insuficiente para o produto: ' . $produto['nome']);
        }

        $preco = (float)$produto['preco'];
        $subtotal = $preco * $quantidade;
        $valor_total += $subtotal;

        $itens[] = [
            'id_produto' => $id_produto,
            'quantidade' => $quantidade,
            'preco' => $preco,
        ];
    }

    $stmtVenda = $conn->prepare("INSERT INTO vendas (id_usuario, data_venda, valor_total) VALUES (:id_usuario, NOW(), :valor_total)");
    $stmtVenda->execute([
        ':id_usuario' => $id_usuario,
        ':valor_total' => $valor_total,
    ]);
    $id_venda = (int)$conn->lastInsertId();

    $stmtItem = $conn->prepare("INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario)
                                VALUES (:id_venda, :id_produto, :quantidade, :preco)");
    $stmtEstoque = $conn->prepare("UPDATE produtos
                                   SET quantidade_estoque = quantidade_estoque - :qtd_baixa
                                   WHERE id_produto = :id_produto
                                     AND quantidade_estoque >= :qtd_minima");

    foreach ($itens as $item) {
        $stmtItem->execute([
            ':id_venda' => $id_venda,
            ':id_produto' => $item['id_produto'],
            ':quantidade' => $item['quantidade'],
            ':preco' => $item['preco'],
        ]);

        $stmtEstoque->execute([
            ':qtd_baixa' => $item['quantidade'],
            ':id_produto' => $item['id_produto'],
            ':qtd_minima' => $item['quantidade'],
        ]);

        if ($stmtEstoque->rowCount() !== 1) {
            throw new Exception('Não foi possível atualizar o estoque.');
        }
    }

    $stmtLog = $conn->prepare("INSERT INTO logs (id_usuario, data_hora, tipo_actividade, descricao)
                               VALUES (:id_usuario, NOW(), :tipo, :descricao)");
    $stmtLog->execute([
        ':id_usuario' => $id_usuario,
        ':tipo' => 'Venda',
        ':descricao' => 'Venda registrada no sistema: ID ' . $id_venda,
    ]);

    $conn->commit();
    flash_set('sucesso', 'Venda registrada com sucesso.');
} catch (Throwable $e) {
    if ($conn && $conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log('Erro ao registrar venda: ' . $e->getMessage());
    flash_set('erro', $e->getMessage());
}

header("Location: ../views/operador/vendas.php");
exit();
?>

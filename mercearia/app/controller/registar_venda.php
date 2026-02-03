<?php
session_start();
include('../config/database.php');

$database = new Database();
$conn = $database->conectar();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter os arrays dos produtos com verificação
    $produtos = $_POST['produto'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];
    $precos = $_POST['preco'] ?? [];
    $id_usuario = $_SESSION['usuario']['id_usuario']; 

    $valor_total = 0;

    // Inserir a venda na tabela vendas
    $sql_venda = "INSERT INTO vendas (id_usuario, data_venda, valor_total) VALUES (:id_usuario, NOW(), :valor_total)";
    $stmt_venda = $conn->prepare($sql_venda);
    $stmt_venda->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
    $stmt_venda->bindValue(":valor_total", $valor_total, PDO::PARAM_STR);

    if ($stmt_venda->execute()) {
        // Pega o id da venda inserida
        $id_venda = $conn->lastInsertId();

        // Loop para inserir os itens da venda
        for ($i = 0; $i < count($produtos); $i++) {
            $id_produto = $produtos[$i];
            $quantidade = $quantidades[$i];
            $preco = $precos[$i];

            // Calcular o valor total da venda
            $valor_total += $preco * $quantidade;

            // Inserir o item da venda na tabela itens_venda
            $sql_item = "INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario) 
                         VALUES (:id_venda, :id_produto, :quantidade, :preco)";
            $stmt_item = $conn->prepare($sql_item);
            $stmt_item->bindValue(":id_venda", $id_venda, PDO::PARAM_INT);
            $stmt_item->bindValue(":id_produto", $id_produto, PDO::PARAM_INT);
            $stmt_item->bindValue(":quantidade", $quantidade, PDO::PARAM_INT);
            $stmt_item->bindValue(":preco", $preco, PDO::PARAM_STR);
            $stmt_item->execute();

            // Atualizar o estoque
            $sql_estoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - :quantidade WHERE id_produto = :id_produto";
            $stmt_estoque = $conn->prepare($sql_estoque);
            $stmt_estoque->bindValue(":quantidade", $quantidade, PDO::PARAM_INT);
            $stmt_estoque->bindValue(":id_produto", $id_produto, PDO::PARAM_INT);
            $stmt_estoque->execute();
        }

        // Atualizar o valor total da venda
        $sql_atualizar_valor = "UPDATE vendas SET valor_total = :valor_total WHERE id_venda = :id_venda";
        $stmt_atualizar_valor = $conn->prepare($sql_atualizar_valor);
        $stmt_atualizar_valor->bindValue(":valor_total", $valor_total, PDO::PARAM_STR);
        $stmt_atualizar_valor->bindValue(":id_venda", $id_venda, PDO::PARAM_INT);
        $stmt_atualizar_valor->execute();

        header("Location: ../views/operador/vendas.php");
    } else {
        echo "Erro ao registrar venda.";
    }

    // Fechar a conexão
    $stmt_venda->closeCursor();
    $stmt_item->closeCursor();
    $stmt_estoque->closeCursor();
    $conn = null;
}
?>

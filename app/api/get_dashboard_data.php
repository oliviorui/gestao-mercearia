<?php
require_once '../config/database.php';
require_once '../helpers/security.php';

require_api_auth('admin');

try {
    $database = new Database();
    $conn = $database->conectar();

    $totais = [];

    $totais['total_vendas'] = (int) $conn->query("SELECT COUNT(id_venda) FROM vendas")->fetchColumn();
    $totais['receita_total'] = (float) $conn->query("SELECT COALESCE(SUM(valor_total), 0) FROM vendas")->fetchColumn();
    $totais['vendas_hoje'] = (int) $conn->query("SELECT COUNT(id_venda) FROM vendas WHERE DATE(data_venda) = CURDATE()")->fetchColumn();
    $totais['receita_hoje'] = (float) $conn->query("SELECT COALESCE(SUM(valor_total), 0) FROM vendas WHERE DATE(data_venda) = CURDATE()")->fetchColumn();
    $totais['vendas_mes'] = (int) $conn->query("SELECT COUNT(id_venda) FROM vendas WHERE YEAR(data_venda) = YEAR(CURDATE()) AND MONTH(data_venda) = MONTH(CURDATE())")->fetchColumn();
    $totais['receita_mes'] = (float) $conn->query("SELECT COALESCE(SUM(valor_total), 0) FROM vendas WHERE YEAR(data_venda) = YEAR(CURDATE()) AND MONTH(data_venda) = MONTH(CURDATE())")->fetchColumn();
    $totais['produtos_estoque'] = (int) $conn->query("SELECT COUNT(id_produto) FROM produtos WHERE quantidade_estoque > 0")->fetchColumn();
    $totais['produtos_baixo_estoque'] = (int) $conn->query("SELECT COUNT(id_produto) FROM produtos WHERE quantidade_estoque > 0 AND quantidade_estoque <= 10")->fetchColumn();
    $totais['produtos_fora_estoque'] = (int) $conn->query("SELECT COUNT(id_produto) FROM produtos WHERE quantidade_estoque = 0")->fetchColumn();

    $stmt = $conn->prepare("SELECT v.id_venda, v.data_venda, v.valor_total, COALESCE(u.nome, 'Usuário removido') AS usuario
        FROM vendas v
        LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
        ORDER BY v.data_venda DESC, v.id_venda DESC
        LIMIT 8");
    $stmt->execute();
    $ultimas_vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT p.nome, SUM(iv.quantidade) AS quantidade, SUM(iv.quantidade * iv.preco_unitario) AS total
        FROM itens_venda iv
        INNER JOIN produtos p ON iv.id_produto = p.id_produto
        GROUP BY p.id_produto, p.nome
        ORDER BY quantidade DESC, total DESC
        LIMIT 5");
    $stmt->execute();
    $top_produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT DATE(data_venda) AS dia, COUNT(id_venda) AS vendas, COALESCE(SUM(valor_total), 0) AS receita
        FROM vendas
        WHERE DATE(data_venda) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(data_venda)
        ORDER BY dia ASC");
    $stmt->execute();
    $vendas_7_dias_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mapa = [];
    foreach ($vendas_7_dias_raw as $linha) {
        $mapa[$linha['dia']] = $linha;
    }

    $vendas_7_dias = [];
    for ($i = 6; $i >= 0; $i--) {
        $dia = date('Y-m-d', strtotime("-$i days"));
        $vendas_7_dias[] = [
            'dia' => $dia,
            'vendas' => isset($mapa[$dia]) ? (int) $mapa[$dia]['vendas'] : 0,
            'receita' => isset($mapa[$dia]) ? (float) $mapa[$dia]['receita'] : 0,
        ];
    }

    $stmt = $conn->prepare("SELECT id_produto, nome, categoria, quantidade_estoque
        FROM produtos
        WHERE quantidade_estoque <= 10
        ORDER BY quantidade_estoque ASC, nome ASC
        LIMIT 8");
    $stmt->execute();
    $estoque_critico = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'totais' => $totais,
        'ultimas_vendas' => $ultimas_vendas,
        'top_produtos' => $top_produtos,
        'vendas_7_dias' => $vendas_7_dias,
        'estoque_critico' => $estoque_critico,
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro ao buscar dados do painel.',
        'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>

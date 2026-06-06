<?php
    require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../helpers/sidebar.php';
    require_role('admin');
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercearia Mahumane - Painel de Administração</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../../public/assets/css/painel.css">
    <link rel="stylesheet" href="../../../public/assets/css/home.css">






<script src="../../../public/assets/js/jquery.js"></script>
    <script src="../../../public/assets/js/calendario.js"></script>
</head>
<body>
    <?php render_sidebar('admin', 'geral'); ?>

    <main class="main-content dashboard-page dashboard-page dashboard-page">
        <div class="dashboard-header">
            <div>
                <p class="dashboard-kicker">Resumo geral</p>
                <h2>Painel de Admin</h2>
            </div>
            <span id="dashboard-status">Carregando dados...</span>
        </div>
        <hr>

        <section class="dashboard-cards">
            <div class="metric-card primary"><span>Receita hoje</span><strong id="receita_hoje">0 MT</strong><small id="vendas_hoje">0 vendas hoje</small></div>
            <div class="metric-card"><span>Receita do mês</span><strong id="receita_mes">0 MT</strong><small id="vendas_mes">0 vendas no mês</small></div>
            <div class="metric-card"><span>Total de vendas</span><strong id="total_vendas">0</strong><small id="receita_total">0 MT faturados</small></div>
            <div class="metric-card warning"><span>Estoque crítico</span><strong id="produtos_baixo_estoque">0</strong><small id="produtos_fora_estoque">0 fora de estoque</small></div>
        </section>

        <section class="dashboard-grid">
            <div class="dashboard-panel wide">
                <div class="panel-title">
                    <h3>Vendas dos últimos 7 dias</h3>
                    <small>Quantidade de vendas por dia</small>
                </div>
                <div id="grafico_vendas" class="bar-chart"></div>
            </div>

            <div class="dashboard-panel">
                <div class="panel-title">
                    <h3>Produtos mais vendidos</h3>
                    <small>Top 5 por quantidade</small>
                </div>
                <ul id="top_produtos" class="ranking-list">
                    <li>Carregando...</li>
                </ul>
            </div>

            <div class="dashboard-panel">
                <div class="panel-title">
                    <h3>Estoque crítico</h3>
                    <small>Produtos com 10 ou menos unidades</small>
                </div>
                <ul id="estoque_critico" class="stock-list">
                    <li>Carregando...</li>
                </ul>
            </div>

            <div class="dashboard-panel wide">
                <div class="panel-title">
                    <h3>Últimas vendas</h3>
                    <small>Registos recentes</small>
                </div>
                <div class="table-wrapper">
                    <table id="ultimas_vendas">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Valor Total</th>
                                <th>Responsável</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="4">Carregando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            function escapeHtml(value) { return $('<div>').text(value ?? '').html(); }

            function moeda(valor) {
                const numero = Number(valor || 0);
                return numero.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MT';
            }

            function dataCurta(data) {
                if (!data) return '-';
                const partes = String(data).split(' ')[0].split('-');
                if (partes.length !== 3) return data;
                return partes[2] + '/' + partes[1];
            }

            function dataHora(data) {
                if (!data) return '-';
                return String(data).replace('T', ' ');
            }

            function carregarDashboard() {
                $.ajax({
                    url: '../../api/get_dashboard_data.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            $('#dashboard-status').text(data.error);
                            return;
                        }

                        const totais = data.totais || {};
                        $('#receita_hoje').text(moeda(totais.receita_hoje));
                        $('#vendas_hoje').text((totais.vendas_hoje || 0) + ' vendas hoje');
                        $('#receita_mes').text(moeda(totais.receita_mes));
                        $('#vendas_mes').text((totais.vendas_mes || 0) + ' vendas no mês');
                        $('#total_vendas').text(totais.total_vendas || 0);
                        $('#receita_total').text(moeda(totais.receita_total) + ' faturados');
                        $('#produtos_baixo_estoque').text(totais.produtos_baixo_estoque || 0);
                        $('#produtos_fora_estoque').text((totais.produtos_fora_estoque || 0) + ' fora de estoque');

                        renderGrafico(data.vendas_7_dias || []);
                        renderTopProdutos(data.top_produtos || []);
                        renderEstoqueCritico(data.estoque_critico || []);
                        renderUltimasVendas(data.ultimas_vendas || []);

                        $('#dashboard-status').text('Atualizado agora');
                    },
                    error: function() {
                        $('#dashboard-status').text('Erro ao carregar dados do dashboard');
                    }
                });
            }

            function renderGrafico(linhas) {
                const max = Math.max(...linhas.map(item => Number(item.vendas || 0)), 1);
                $('#grafico_vendas').empty();

                linhas.forEach(function(item) {
                    const vendas = Number(item.vendas || 0);
                    const altura = Math.max((vendas / max) * 100, vendas > 0 ? 8 : 2);
                    $('#grafico_vendas').append(
                        '<div class="bar-item">' +
                            '<span class="bar-value">' + escapeHtml(vendas) + '</span>' +
                            '<div class="bar-track"><div class="bar-fill" style="height:' + altura + '%"></div></div>' +
                            '<small>' + escapeHtml(dataCurta(item.dia)) + '</small>' +
                        '</div>'
                    );
                });
            }

            function renderTopProdutos(produtos) {
                $('#top_produtos').empty();
                if (!produtos.length) {
                    $('#top_produtos').append('<li>Nenhum produto vendido ainda.</li>');
                    return;
                }
                produtos.forEach(function(produto, index) {
                    $('#top_produtos').append(
                        '<li><strong>#' + (index + 1) + ' ' + escapeHtml(produto.nome) + '</strong><span>' +
                        escapeHtml(produto.quantidade) + ' un. · ' + moeda(produto.total) + '</span></li>'
                    );
                });
            }

            function renderEstoqueCritico(produtos) {
                $('#estoque_critico').empty();
                if (!produtos.length) {
                    $('#estoque_critico').append('<li>Sem produtos em estoque crítico.</li>');
                    return;
                }
                produtos.forEach(function(produto) {
                    const qtd = Number(produto.quantidade_estoque || 0);
                    const status = qtd === 0 ? 'Fora de estoque' : qtd + ' unidades';
                    $('#estoque_critico').append(
                        '<li><strong>' + escapeHtml(produto.nome) + '</strong><span>' + escapeHtml(status) + '</span></li>'
                    );
                });
            }

            function renderUltimasVendas(vendas) {
                $('#ultimas_vendas tbody').empty();
                if (!vendas.length) {
                    $('#ultimas_vendas tbody').append('<tr><td colspan="4">Nenhuma venda encontrada.</td></tr>');
                    return;
                }
                vendas.forEach(function(venda) {
                    $('#ultimas_vendas tbody').append(
                        '<tr>' +
                            '<td>' + escapeHtml(venda.id_venda) + '</td>' +
                            '<td>' + escapeHtml(dataHora(venda.data_venda)) + '</td>' +
                            '<td>' + escapeHtml(moeda(venda.valor_total)) + '</td>' +
                            '<td>' + escapeHtml(venda.usuario) + '</td>' +
                        '</tr>'
                    );
                });
            }

            carregarDashboard();
        });
    </script>
</body>
</html>

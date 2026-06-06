<?php
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../helpers/sidebar.php';
require_auth('admin', '../login.php');
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Mercearia Mahumane - Estoque</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../../public/assets/css/painel.css">




<script src="../../../public/assets/js/jquery.js"></script>
</head>
<body>
    <?php render_sidebar('admin', 'estoque'); ?>

    <main class="main-content estoque-page estoque-page">
        <h2>Estoque Disponível</h2>
        <hr>
        <form action="#" id="search-form">
            <input type="text" name="" id="" placeholder="Busque pelo nome ou ID">
            <button type="submit">
                <img src="../../../public/assets/images/icons/search_24dp.svg" alt="" title="Buscar">
            </button>
        </form>
        <div class="itens">
            <!-- Lista de produtos será carregada aqui -->
        </div>
    </main>
    <script>
        $(document).ready(function() {
            // Função para carregar os produtos com base no filtro
            function carregarProdutos(filtro = '') {
                $.ajax({
                    url: '../../api/get_produtos.php',
                    method: 'GET',
                    data: { search: filtro }, // Passando o filtro na URL
                    dataType: 'json',
                    success: function(data) {
                        $('.itens').empty(); // Limpar itens antes de adicionar os novos
                        
                        if (data.message) {
                            $('.itens').append('<p>' + data.message + '</p>');
                        } else {
                            $.each(data, function(index, produto) {
                                $('.itens').append(
                                    '<ul>' +
                                        '<li># ' + produto.id_produto + '</li>' +
                                        '<li>' + produto.nome + '</li>' +
                                        '<li>' + produto.quantidade_estoque + ' unidades</li>' +
                                    '</ul>'
                                );
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Erro ao carregar os dados: ' + error);
                    }
                });
            }

            // Carregar todos os produtos ao carregar a página
            carregarProdutos();

            // Evento 'input' no campo de pesquisa (será acionado a cada tecla digitada)
            $('#search-form input').on('input', function() {
                var searchTerm = $(this).val(); // Obter o texto do campo de pesquisa
                carregarProdutos(searchTerm); // Passar o termo de pesquisa
            });
        });
    </script>
</body>
</html>

<?php
    session_start();
    // Verifica se o usuário logado é do tipo 'admin'
    if ($_SESSION['usuario']['tipo_usuario'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Banca Mahumane - Consulta de Estoque</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="../../../public/assets/css/geral.css">
    <link rel="stylesheet" href="../../../public/assets/css/footer-admin.css">
    <link rel="stylesheet" href="../../../public/assets/css/estoque.css">
    
    <script src="../../../public/assets/js/jquery.js"></script>
    <script src="../../../public/assets/js/geral.js"></script>
</head>
<body>
    <div class="sidebar">
        <div class="perfil">
            <img src="../../../public/assets/images/admin.png" alt="Admin">
            <p><?php echo $_SESSION['usuario']['nome']; ?></p>
        </div>
        <hr>
        <nav>
            <a href="geral.php">Geral</a>
            <a href="produtos.php">Cadastrar Produtos</a>
            <a href="estoque.php" class="atual">Consultar Estoque</a>
            <a href="usuarios.php">Gerenciar Usuários</a>
            <a href="logs.php">Atividades no sistema</a>
        </nav>
        
        <form action="../../controller/autenticar.php" method="POST" id="logout">
            <hr>
            <input type="hidden" name="acao" value="logout">
            <input type="image" src="../../../public/assets/images/icons/logout_24dp.svg" alt="Logout logo" id="img-logout" title="Terminar Sessão">
        </form>
    </div>

    <div class="main-content">
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
    </div>
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

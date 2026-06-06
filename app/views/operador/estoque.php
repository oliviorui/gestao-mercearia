<?php
    session_start();
    // Verifica se o usuário logado é do tipo 'operador'
    if ($_SESSION['usuario']['tipo_usuario'] !== 'operador') {
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
    <link rel="stylesheet" href="../../../public/assets/css/footer-vendedor.css">
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
            <a href="vendas.php">Painel de vendas</a>
            <a href="estoque.php" class="atual">Consultar Estoque</a>
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
            function carregarProdutos(filtro = '') {
                $.ajax({
                    url: '../../api/get_produtos.php',
                    method: 'GET',
                    data: { search: filtro },
                    dataType: 'json',
                    success: function(data) {
                        $('.itens').empty();
                        
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

            carregarProdutos();

            $('#search-form input').on('input', function() {
                var searchTerm = $(this).val();
                carregarProdutos(searchTerm);
            });
        });
    </script>
</body>
</html>

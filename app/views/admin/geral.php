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
    
    <title>Banca Mahumane - Painel de Admininstrador</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="../../../public/assets/css/geral.css">
    <link rel="stylesheet" href="../../../public/assets/css/footer-admin.css">
    <link rel="stylesheet" href="../../../public/assets/css/calendario.css">
    <link rel="stylesheet" href="../../../public/assets/css/tabela.css">
    <link rel="stylesheet" href="../../../public/assets/css/home.css">
    
    <script src="../../../public/assets/js/jquery.js"></script>
    <script src="../../../public/assets/js/geral.js"></script>
    <script src="../../../public/assets/js/calendario.js"></script>
</head>
<body>
    <div class="sidebar">
        <div class="perfil">
            <img src="../../../public/assets/images/admin.png" alt="Admin">
            <p><?php echo $_SESSION['usuario']['nome']; ?></p>
        </div>
        <hr>
        <nav>
            <a href="geral.php" class="atual">Geral</a>
            <a href="produtos.php">Cadastrar Produtos</a>
            <a href="estoque.php">Consultar Estoque</a>
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
        <h2>Painel de Admin</h2>
        <hr>
        <div id="cards">
            <div class="card" id="first">Total de Vendas: NN</div>
            <div class="card" id="second">Produtos no Estoque: NN</div>
            <div class="card" id="third">Produtos fora do Estoque: NN</div>
        </div>

        <div class="calendario">
            <div class="header">
              <button id="prev">&lt;</button>
              <h2 id="mesAno"></h2>
              <button id="next">&gt;</button>
            </div>
            <div class="dias-semana">
              <span>Dom</span><span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span>
            </div>
            <div class="dias" id="dias"></div>
        </div>

        <table border="1" id="itens_venda">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Valor Total</th>
                    <th>Responsável</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dados serão inseridos aqui -->
                <tr>
                    <td colspan="5" style="text-align: center;">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>
            $(document).ready(function() {
                $.ajax({
                    url: '../../api/get_dashboard_data.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#first').text('Total de Vendas: ' + data.total_vendas);
                        $('#second').text('Produtos no Estoque: ' + data.produtos_estoque);
                        $('#third').text('Produtos fora do Estoque: ' + data.produtos_fora_estoque);
                    },
                    error: function(xhr, status, error) {
                        alert('Erro ao carregar os dados dos cards: ' + error);
                    }
                });

                $.ajax({
                    url: '../../api/get_vendas.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('table tbody').empty();
                        
                        $.each(data, function(index, venda) {
                            $('table tbody').append(
                                '<tr>' +
                                    '<td>' + venda.id_venda + '</td>' +
                                    '<td>' + venda.data_venda + '</td>' +
                                    '<td>' + venda.valor_total + '</td>' +
                                    '<td>' + venda.usuario + '</td>' +
                                '</tr>'
                            );
                        });
                    },
                    error: function(xhr, status, error) {
                        alert('Erro ao carregar os dados: ' + error);
                    }
                });
            });
        </script>
</body>
</html>

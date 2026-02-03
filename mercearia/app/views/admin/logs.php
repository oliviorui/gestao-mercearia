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
    
    <title>Banca Mahumane - Actividades no Sistema</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="../../../public/assets/css/geral.css">
    <link rel="stylesheet" href="../../../public/assets/css/footer-admin.css">
    <link rel="stylesheet" href="../../../public/assets/css/tabela.css">

    <script src="../../../public/assets/js/jquery.js"></script>
    <script src="../../../public/assets/js/geral.js"></script>

    <style>
        .main-content {
            overflow-y: scroll;
        }
    </style>
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
            <a href="estoque.php">Consultar Estoque</a>
            <a href="usuarios.php">Gerenciar Usuários</a>
            <a href="logs.php" class="atual">Atividades no sistema</a>
        </nav>
        
        <form action="../../controller/autenticar.php" method="POST" id="logout">
            <hr>
            <input type="hidden" name="acao" value="logout">
            <input type="image" src="../../../public/assets/images/icons/logout_24dp.svg" alt="Logout logo" id="img-logout" title="Terminar Sessão">
        </form>
    </div>

    <div class="main-content">
        <h2>Atividades no Sistema</h2>
        <hr>
        <table id="logs-table" border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Usuário</th>
                    <th>Tipo de Atividade</th>
                    <th>Descrição</th>
                    <th>Data e Hora</th>
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
                    url: '../../api/get_logs.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#logs-table tbody').empty();
                        
                        $.each(data, function(index, log) {
                            $('#logs-table tbody').append(
                                '<tr>' +
                                    '<td>' + log.id_log + '</td>' +
                                    '<td>' + log.usuario + '</td>' +
                                    '<td>' + log.tipo_actividade + '</td>' +
                                    '<td>' + log.descricao + '</td>' +
                                    '<td>' + log.data_hora + '</td>' +
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

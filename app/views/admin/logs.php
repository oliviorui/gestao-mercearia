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
    
    <title>Mercearia Mahumane - Atividades do Sistema</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../../public/assets/css/painel.css">




<script src="../../../public/assets/js/jquery.js"></script>

    <style>
        .main-content {
            overflow-y: scroll;
        }
    </style>
</head>
<body>
    <?php render_sidebar('admin', 'logs'); ?>

    <main class="main-content logs-page logs-page">
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
    </main>
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

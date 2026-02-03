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
    
    <title>Banca Mahumane - Gerenciar de Usuários</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="../../../public/assets/css/geral.css">
    <link rel="stylesheet" href="../../../public/assets/css/footer-admin.css">
    <link rel="stylesheet" href="../../../public/assets/css/usuarios.css">
    
    <script src="../../../public/assets/js/jquery.js"></script>
    <script src="../../../public/assets/js/validate.js"></script>
    <script src="../../../public/assets/js/geral.js"></script>
    <script src="../../../public/assets/js/validacoes.js"></script>

    <style>
        input.error {
            color: red;
            border: 1px solid red;
        }

        .error::placeholder, label.error {
            color: red;
            font-size: 0.9em;
            margin: 0 9px;
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
            <a href="usuarios.php" class="atual">Gerenciar Usuários</a>
            <a href="logs.php">Atividades no sistema</a>
        </nav>
        
        <form action="../../controller/autenticar.php" method="POST" id="logout">
            <hr>
            <input type="hidden" name="acao" value="logout">
            <input type="image" src="../../../public/assets/images/icons/logout_24dp.svg" alt="Logout logo" id="img-logout" title="Terminar Sessão">
        </form>
    </div>

    <div class="main-content">
        <h2>Gerenciamento de Usuários</h2>
        <hr>
        <form action="../../controller/crud_usuario.php" method="POST" id="desaparece">
            <fieldset>
                <legend>Criar usuário</legend>
                <input type="hidden" name="acao" value="cadastrar">
                <table>
                    <tr>
                        <td> <label for="nome">Nome:</label> </td>
                        <td> <input type="text" name="nome"> </td>
                    </tr>
                    <tr>
                        <td> <label for="email">Email:</label> </td>
                        <td> <input type="email" name="email"> </td>
                    </tr>
                    <tr>
                        <td> <label for="senha">Senha:</label> </td>
                        <td> <input type="password" name="senha"> </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="radio" name="tipo_usuario" id="admin" value="admin">
                            <label for="admin">Admin</label>
                        </td>
                        <td>
                            <input type="radio" name="tipo_usuario" id="operador" value="operador" checked>
                            <label for="operador">Operador</label>
                        </td>
                    </tr>
                </table>
                <button type="submit" id="enviar">Cadastrar</button>
            </fieldset>
        </form>
        <!-- Formulário de Edição -->
        <form action="../../controller/crud_usuario.php" method="POST" id="form-edicao" style="display: none;">
            <fieldset>
                <legend>Editar Usuário</legend>
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id_usuario">
                <table>
                    <tr>
                        <td> <label for="nome">Nome:</label> </td>
                        <td> <input type="text" name="nome"> </td>
                    </tr>
                    <tr>
                        <td> <label for="email">Email:</label> </td>
                        <td> <input type="email" name="email"> </td>
                    </tr>
                    <tr>
                        <td> <label for="senha">Senha:</label> </td>
                        <td> <input type="password" name="senha" placeholder="Digite uma nova senha (opcional)"> </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="radio" name="tipo_usuario" id="admin" value="admin">
                            <label for="admin">Admin</label>
                        </td>
                        <td>
                            <input type="radio" name="tipo_usuario" id="operador" value="operador" checked>
                            <label for="operador">Operador</label>
                        </td>
                    </tr>
                </table>
                <button type="submit" class="editarBtn">Atualizar</button>
                <button type="button" id="cancelar-edicao" class="editarBtn" onclick="$('#form-edicao').hide(); $('#desaparece').show();">Cancelar</button>
            </fieldset>
        </form>

        <div class="itens">
             <!-- Lista de usuários será carregada aqui -->
        </div>
    </div>
    <script>
        $(document).ready(function() {
            function carregarUsuarios() {
                $.ajax({
                    url: '../../api/get_usuarios.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('.itens').empty();

                        $.each(data, function(index, usuario) {
                            $('.itens').append(
                                '<ul>' +
                                    '<li>' + usuario.nome + '</li>' +
                                    '<li>' + usuario.email + '</li>' +
                                    '<li>' + usuario.tipo_usuario + '</li>' +
                                    '<li>' +
                                        '<input type="image" src="../../../public/assets/images/icons/edit_24dp.svg" class="editar" data-id="' + usuario.id_usuario + '" data-nome="' + usuario.nome + '" data-email="' + usuario.email + '" data-tipo="' + usuario.tipo_usuario +'">' +
                                        '<input type="image" src="../../../public/assets/images/icons/delete_24dp.svg" class="excluir" data-id="' + usuario.id_usuario + '">' +
                                    '</li>' +
                                '</ul>'
                            );
                        });
                    }
                });
            }
            carregarUsuarios();

            $(document).on('click', '.editar', function() {
                let id = $(this).data('id');
                let nome = $(this).data('nome');
                let email = $(this).data('email');
                let tipo = $(this).data('tipo');

                $('#form-edicao input[name=id_usuario]').val(id);
                $('#form-edicao input[name=nome]').val(nome);
                $('#form-edicao input[name=email]').val(email);
                $('#form-edicao input[name=tipo_usuario][value="' + tipo + '"]').prop('checked', true);

                $('#form-edicao').show();
                $('#desaparece').hide();
            });

            $(document).on('click', '.excluir', function() {
                var idUsuario = $(this).data('id');
                
                if (confirm("Tem certeza de que deseja excluir este usuário?")) {
                    $.ajax({
                        url: '../../controller/crud_usuario.php',
                        method: 'POST',
                        data: { acao: 'excluir', id_usuario: idUsuario },
                        success: function(response) {
                            alert("Usuario excluido com sucesso!");
                            carregarUsuarios();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
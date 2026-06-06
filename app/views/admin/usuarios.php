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
    <title>Mercearia Mahumane - Usuários</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../../public/assets/css/painel.css">
    <script src="../../../public/assets/js/jquery.js"></script>
    <script src="../../../public/assets/js/validate.js"></script>
    <script src="../../../public/assets/js/validacoes.js"></script>
</head>
<body>
    <?php render_sidebar('admin', 'usuarios'); ?>
    <main class="main-content usuarios-page">
        <h2>Gerenciamento de Usuários</h2>
        <hr>
        <div class="content-grid">
            <section class="form-card">
                <form action="../../controller/crud_usuario.php" method="POST" id="desaparece">
                    <fieldset>
                        <legend>Criar Usuário</legend>
                        <input type="hidden" name="acao" value="cadastrar">
                        <?php echo csrf_field(); ?>
                        <table>
                            <tr><td><label for="nome">Nome:</label></td><td><input type="text" name="nome" id="nome"></td></tr>
                            <tr><td><label for="email">Email:</label></td><td><input type="email" name="email" id="email"></td></tr>
                            <tr><td><label for="senha">Senha:</label></td><td><input type="password" name="senha" id="senha"></td></tr>
                            <tr>
                                <td><label><input type="radio" name="tipo_usuario" value="admin"> Admin</label></td>
                                <td><label><input type="radio" name="tipo_usuario" value="operador" checked> Operador</label></td>
                            </tr>
                        </table>
                        <button type="submit" id="enviar">Cadastrar</button>
                    </fieldset>
                </form>

                <form action="../../controller/crud_usuario.php" method="POST" id="form-edicao" style="display:none;">
                    <fieldset>
                        <legend>Editar Usuário</legend>
                        <input type="hidden" name="acao" value="editar">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="id_usuario">
                        <table>
                            <tr><td><label>Nome:</label></td><td><input type="text" name="nome"></td></tr>
                            <tr><td><label>Email:</label></td><td><input type="email" name="email"></td></tr>
                            <tr><td><label>Senha:</label></td><td><input type="password" name="senha" placeholder="Nova senha (opcional)"></td></tr>
                            <tr>
                                <td><label><input type="radio" name="tipo_usuario" value="admin"> Admin</label></td>
                                <td><label><input type="radio" name="tipo_usuario" value="operador" checked> Operador</label></td>
                            </tr>
                        </table>
                        <button type="submit" class="editarBtn">Atualizar</button>
                        <button type="button" id="cancelar-edicao" class="editarBtn">Cancelar</button>
                    </fieldset>
                </form>
            </section>
            <section class="list-card">
                <div class="itens"></div>
            </section>
        </div>
    </main>
    <script>
        $(document).ready(function() {
            function text(value) { return value == null ? '' : String(value); }

            function carregarUsuarios() {
                $.ajax({
                    url: '../../api/get_usuarios.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('.itens').empty();
                        if (!Array.isArray(data) || data.length === 0) {
                            $('.itens').append('<p>Nenhum usuário encontrado.</p>');
                            return;
                        }
                        $.each(data, function(index, usuario) {
                            const item = $('<ul></ul>');
                            item.append($('<li></li>').text(text(usuario.nome)));
                            item.append($('<li></li>').text(text(usuario.email)));
                            item.append($('<li></li>').text(text(usuario.tipo_usuario)));
                            const actions = $('<li></li>');
                            $('<input>', { type:'image', src:'../../../public/assets/images/icons/edit_24dp.svg', class:'editar' })
                                .attr('data-id', usuario.id_usuario)
                                .attr('data-nome', usuario.nome)
                                .attr('data-email', usuario.email)
                                .attr('data-tipo', usuario.tipo_usuario)
                                .appendTo(actions);
                            $('<input>', { type:'image', src:'../../../public/assets/images/icons/delete_24dp.svg', class:'excluir' })
                                .attr('data-id', usuario.id_usuario)
                                .appendTo(actions);
                            item.append(actions);
                            $('.itens').append(item);
                        });
                    }
                });
            }
            carregarUsuarios();

            $(document).on('click', '.editar', function() {
                $('#form-edicao input[name=id_usuario]').val($(this).data('id'));
                $('#form-edicao input[name=nome]').val($(this).data('nome'));
                $('#form-edicao input[name=email]').val($(this).data('email'));
                $('#form-edicao input[name=tipo_usuario][value="' + $(this).data('tipo') + '"]').prop('checked', true);
                $('#form-edicao').show();
                $('#desaparece').hide();
            });

            $('#cancelar-edicao').on('click', function() {
                $('#form-edicao').hide();
                $('#desaparece').show();
            });

            $(document).on('click', '.excluir', function() {
                var idUsuario = $(this).data('id');
                if (confirm('Tem certeza de que deseja excluir este usuário?')) {
                    $.ajax({
                        url: '../../controller/crud_usuario.php',
                        method: 'POST',
                        data: { acao: 'excluir', id_usuario: idUsuario, csrf_token: '<?php echo e(csrf_token()); ?>' },
                        success: function() {
                            alert('Usuário excluído com sucesso!');
                            carregarUsuarios();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

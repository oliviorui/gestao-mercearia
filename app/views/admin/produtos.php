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
    
    <title>Banca Mahumane - Cadastro de Produtos</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">
    
    <link rel="stylesheet" href="../../../public/assets/css/geral.css">
    <link rel="stylesheet" href="../../../public/assets/css/footer-admin.css">
    <link rel="stylesheet" href="../../../public/assets/css/produtos.css">
    
    <script src="../../../public/assets/js/jquery.js"></script>
    <script src="../../../public/assets/js/validate.js"></script>
    <script src="../../../public/assets/js/geral.js"></script>
    <script src="../../../public/assets/js/validacoes.js"></script>

    <style>
        input.error, textarea.error {
            color: red;
            border: 1px solid red;
        }
        textarea.error, select.error {
            border: 1px solid red;
        }

        .error::placeholder, label.error {
            color: red;
            font-size: 0.9em;
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
            <a href="produtos.php" class="atual">Cadastrar Produtos</a>
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
        <h2>Gerenciamento de produtos</h2>
        <hr>
        <form action="../../controller/crud_produto.php" method="POST" id="form-produto">
            <fieldset>
                <legend>Inserir produto</legend>
                <input type="hidden" name="acao" value="cadastrar">
                <table>
                    <tr>
                        <td> <label for="nome">Nome:</label> </td>
                        <td> <input type="text" name="nome"> </td>
                    </tr>
                    <tr>
                        <td> <label for="categoria">Categoria:</label> </td>
                        <td> 
                            <select name="categoria" id="categoria">
                                <option value="">Escolha a categoria</option>
                                <option value="Alimentos Básicos">Alimentos Básicos</option>
                                <option value="Laticínios e Ovos">Laticínios e Ovos</option>
                                <option value="Enlatados e Conservas">Enlatados e Conservas</option>
                                <option value="Pães e Biscoitos">Pães e Biscoitos</option>

                                <option value="Bebidas">Bebidas</option>
                                <option value="Doces e Guloseimas">Doces e Guloseimas</option>
                                <option value="Produtos de Limpeza">Produtos de Limpeza</option>
                                <option value="Produtos de Higiene">Produtos de Higiene</option>
                            </select> 
                        </td>
                    </tr>
                    <tr>
                        <td> <label for="preco">Preço:</label> </td>
                        <td> <input type="number" name="preco"> </td>
                    </tr>
                    <tr>
                        <td> <label for="quant_estoque">Quantidade:</label> </td>
                        <td> <input type="number" name="quant_estoque" step="1"> </td>
                    </tr>
                </table>
                <br>
                <textarea name="descricao" id="descricao" placeholder="Descrição"></textarea>
                <button type="submit" id="enviar">Cadastrar</button>
            </fieldset>
        </form>
        <!-- Formulário de Edição -->
        <div id="form_editar" style="display: none;">
            <form id="editar_produto">
                <fieldset>
                    <legend>Editar Produto</legend>
                    <input type="hidden" name="id_produto" id="edit_id">
                    
                    <table>
                        <tr>
                            <td><label for="edit_nome">Nome:</label></td>
                            <td><input type="text" name="nome" id="edit_nome"></td>
                        </tr>
                        <tr>
                            <td><label for="edit_categoria">Categoria:</label></td>
                            <td>
                                <select name="categoria" id="edit_categoria">
                                    <option value="">Escolha a categoria</option>
                                    <option value="Alimentos Básicos">Alimentos Básicos</option>
                                    <option value="Laticínios e Ovos">Laticínios e Ovos</option>
                                    <option value="Enlatados e Conservas">Enlatados e Conservas</option>
                                    <option value="Pães e Biscoitos">Pães e Biscoitos</option>

                                    <option value="Bebidas">Bebidas</option>
                                    <option value="Doces e Guloseimas">Doces e Guloseimas</option>
                                    <option value="Produtos de Limpeza">Produtos de Limpeza</option>
                                    <option value="Produtos de Higiene">Produtos de Higiene</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="edit_preco">Preço:</label></td>
                            <td><input type="number" name="preco" id="edit_preco"></td>
                        </tr>
                        <tr>
                            <td><label for="edit_quantidade">Quantidade:</label></td>
                            <td><input type="number" name="quantidade_estoque" id="edit_quantidade"></td>
                        </tr>
                    </table>
                    
                    <br>
                    <textarea name="descricao" id="edit_descricao"></textarea>
                    
                    <button type="submit" class="editarBtn">Salvar</button>
                    <button type="button" class="editarBtn" onclick="$('#form_editar').hide(); $('#form-produto').show();">Cancelar</button>
                </fieldset>
            </form>
        </div>
        <div class="itens">
            <!-- TUDO AQUI -->
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Carregar produtos
            function carregarProdutos() {
                $.ajax({
                    url: '../../api/get_produtos.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('.itens').empty();
                        
                        $.each(data, function(index, produto) {
                            $('.itens').append(
                                '<ul>' +
                                    '<li>' + produto.nome + '</li>' +
                                    '<li>' + produto.categoria + '</li>' +
                                    '<li style="font-weight: 600;">' + produto.preco + ' Mt</li>' +
                                    '<li>' + produto.quantidade_estoque + '</li>' +
                                    '<li>' + produto.descricao + '</li>' +
                                    '<li>' +
                                        '<input type="image" src="../../../public/assets/images/icons/edit_24dp.svg" class="editar" data-id="' + produto.id_produto + '" data-nome="' + produto.nome + '" data-categoria="' + produto.categoria + '" data-preco="' + produto.preco + '" data-quantidade="' + produto.quantidade_estoque + '" data-descricao="' + produto.descricao + '">' +
                                        '<input type="image" src="../../../public/assets/images/icons/delete_24dp.svg" class="deletar" data-id="' + produto.id_produto + '">' +
                                    '</li>' +
                                '</ul>'
                            );
                        });
                    },
                    error: function(xhr, status, error) {
                        alert('Erro ao carregar os dados: ' + error);
                    }
                });
            }
            carregarProdutos();

            // Mostrar formulário de edição ao clicar no botão de editar
            $(document).on('click', '.editar', function() {
                let id = $(this).data('id');
                let nome = $(this).data('nome');
                let categoria = $(this).data('categoria');
                let preco = $(this).data('preco');
                let quantidade = $(this).data('quantidade');
                let descricao = $(this).data('descricao');

                $('#edit_id').val(id);
                $('#edit_nome').val(nome);
                $('#edit_categoria').val(categoria);
                $('#edit_preco').val(preco);
                $('#edit_quantidade').val(quantidade);
                $('#edit_descricao').val(descricao);

                $("#form-produto").hide();
                $('#form_editar').show();
            });

            // Enviar formulário de edição
            $('#editar_produto').submit(function(event) {
                event.preventDefault();

                 // Verificar se o formulário é válido
                if ($('#editar_produto').valid()) {
                    $.ajax({
                        url: '../../controller/crud_produto.php',
                        method: 'POST',
                        data: $(this).serialize() + '&acao=editar',
                        success: function(response) {
                            alert("Dados editados com sucesso!");
                            $('#form_editar').hide();
                            carregarProdutos();
                            $("#form-produto").show();
                        }
                    });
                } else {
                    // A validação falhou, não envia o formulário
                    alert("Por favor, corrija os erros no formulário.");
                }
            });

            // Deletar produto
            $(document).on('click', '.deletar', function() {
                let id = $(this).data('id');
                if (confirm('Tem certeza que deseja excluir este produto?')) {
                    $.ajax({
                        url: '../../controller/crud_produto.php',
                        method: 'POST',
                        data: { id_produto: id, acao: 'deletar' },
                        success: function() {
                            alert("Produto excluido com sucesso!");
                            carregarProdutos();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

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
    <title>Mercearia Mahumane - Produtos</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../../public/assets/css/painel.css">
    <script src="../../../public/assets/js/jquery.js"></script>
    <script src="../../../public/assets/js/validate.js"></script>
    <script src="../../../public/assets/js/validacoes.js"></script>
</head>
<body>
    <?php render_sidebar('admin', 'produtos'); ?>
    <main class="main-content produtos-page">
        <h2>Gerenciamento de Produtos</h2>
        <hr>
        <div class="content-grid">
            <section class="form-card">
                <form action="../../controller/crud_produto.php" method="POST" id="form-produto">
                    <fieldset>
                        <legend>Inserir Produto</legend>
                        <input type="hidden" name="acao" value="cadastrar">
                        <?php echo csrf_field(); ?>
                        <table>
                            <tr><td><label for="nome">Nome:</label></td><td><input type="text" name="nome" id="nome"></td></tr>
                            <tr><td><label for="categoria">Categoria:</label></td><td><select name="categoria" id="categoria"><option value="">Escolha a categoria</option>
                                    <option value="Alimentos Básicos">Alimentos Básicos</option>
                                    <option value="Laticínios e Ovos">Laticínios e Ovos</option>
                                    <option value="Enlatados e Conservas">Enlatados e Conservas</option>
                                    <option value="Pães e Biscoitos">Pães e Biscoitos</option>
                                    <option value="Bebidas">Bebidas</option>
                                    <option value="Doces e Guloseimas">Doces e Guloseimas</option>
                                    <option value="Produtos de Limpeza">Produtos de Limpeza</option>
                                    <option value="Produtos de Higiene">Produtos de Higiene</option></select></td></tr>
                            <tr><td><label for="preco">Preço:</label></td><td><input type="number" name="preco" id="preco" step="0.01"></td></tr>
                            <tr><td><label for="quant_estoque">Quantidade:</label></td><td><input type="number" name="quant_estoque" id="quant_estoque" step="1"></td></tr>
                        </table>
                        <textarea name="descricao" id="descricao" placeholder="Descrição"></textarea>
                        <button type="submit" id="enviar">Cadastrar</button>
                    </fieldset>
                </form>

                <div id="form_editar" style="display:none;">
                    <form id="editar_produto">
                        <fieldset>
                            <legend>Editar Produto</legend>
                            <input type="hidden" name="id_produto" id="edit_id">
                            <?php echo csrf_field(); ?>
                            <table>
                                <tr><td><label for="edit_nome">Nome:</label></td><td><input type="text" name="nome" id="edit_nome"></td></tr>
                                <tr><td><label for="edit_categoria">Categoria:</label></td><td><select name="categoria" id="edit_categoria"><option value="">Escolha a categoria</option>
                                    <option value="Alimentos Básicos">Alimentos Básicos</option>
                                    <option value="Laticínios e Ovos">Laticínios e Ovos</option>
                                    <option value="Enlatados e Conservas">Enlatados e Conservas</option>
                                    <option value="Pães e Biscoitos">Pães e Biscoitos</option>
                                    <option value="Bebidas">Bebidas</option>
                                    <option value="Doces e Guloseimas">Doces e Guloseimas</option>
                                    <option value="Produtos de Limpeza">Produtos de Limpeza</option>
                                    <option value="Produtos de Higiene">Produtos de Higiene</option></select></td></tr>
                                <tr><td><label for="edit_preco">Preço:</label></td><td><input type="number" name="preco" id="edit_preco" step="0.01"></td></tr>
                                <tr><td><label for="edit_quantidade">Quantidade:</label></td><td><input type="number" name="quantidade_estoque" id="edit_quantidade" step="1"></td></tr>
                            </table>
                            <textarea name="descricao" id="edit_descricao" placeholder="Descrição"></textarea>
                            <button type="submit" class="editarBtn">Salvar</button>
                            <button type="button" class="editarBtn" id="cancelar-edicao">Cancelar</button>
                        </fieldset>
                    </form>
                </div>
            </section>
            <section class="list-card">
                <div class="itens"></div>
            </section>
        </div>
    </main>
    <script>
        $(document).ready(function() {
            function text(value) { return value == null ? '' : String(value); }

            function carregarProdutos() {
                $.ajax({
                    url: '../../api/get_produtos.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('.itens').empty();
                        if (!Array.isArray(data) || data.length === 0) {
                            $('.itens').append('<p>Nenhum produto encontrado.</p>');
                            return;
                        }
                        $.each(data, function(index, produto) {
                            const item = $('<ul></ul>');
                            item.append($('<li></li>').text(text(produto.nome)));
                            item.append($('<li></li>').text(text(produto.categoria)));
                            item.append($('<li></li>').css('font-weight','700').text(text(produto.preco) + ' Mt'));
                            item.append($('<li></li>').text(text(produto.quantidade_estoque)));
                            item.append($('<li></li>').text(text(produto.descricao)));

                            const actions = $('<li></li>');
                            $('<input>', { type:'image', src:'../../../public/assets/images/icons/edit_24dp.svg', class:'editar' })
                                .attr('data-id', produto.id_produto)
                                .attr('data-nome', produto.nome)
                                .attr('data-categoria', produto.categoria)
                                .attr('data-preco', produto.preco)
                                .attr('data-quantidade', produto.quantidade_estoque)
                                .attr('data-descricao', produto.descricao)
                                .appendTo(actions);
                            $('<input>', { type:'image', src:'../../../public/assets/images/icons/delete_24dp.svg', class:'deletar' })
                                .attr('data-id', produto.id_produto)
                                .appendTo(actions);
                            item.append(actions);
                            $('.itens').append(item);
                        });
                    },
                    error: function(xhr, status, error) { alert('Erro ao carregar os dados: ' + error); }
                });
            }
            carregarProdutos();

            $(document).on('click', '.editar', function() {
                $('#edit_id').val($(this).data('id'));
                $('#edit_nome').val($(this).data('nome'));
                $('#edit_categoria').val($(this).data('categoria'));
                $('#edit_preco').val($(this).data('preco'));
                $('#edit_quantidade').val($(this).data('quantidade'));
                $('#edit_descricao').val($(this).data('descricao'));
                $('#form-produto').hide();
                $('#form_editar').show();
            });

            $('#cancelar-edicao').on('click', function() {
                $('#form_editar').hide();
                $('#form-produto').show();
            });

            $('#editar_produto').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    url: '../../controller/crud_produto.php',
                    method: 'POST',
                    data: $(this).serialize() + '&acao=editar',
                    success: function() {
                        alert('Dados editados com sucesso!');
                        $('#form_editar').hide();
                        $('#form-produto').show();
                        carregarProdutos();
                    }
                });
            });

            $(document).on('click', '.deletar', function() {
                let id = $(this).data('id');
                if (confirm('Tem certeza que deseja excluir este produto?')) {
                    $.ajax({
                        url: '../../controller/crud_produto.php',
                        method: 'POST',
                        data: { id_produto: id, acao: 'deletar', csrf_token: '<?php echo e(csrf_token()); ?>' },
                        success: function() {
                            alert('Produto excluído com sucesso!');
                            carregarProdutos();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

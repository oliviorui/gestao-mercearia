<?php
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../helpers/sidebar.php';
require_auth(['admin', 'operador'], '../login.php');
$flash = flash_get();
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mercearia Mahumane - Vendas</title>
    <link rel="icon" href="../../../public/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../../public/assets/css/painel.css">





<script src="../../../public/assets/js/jquery.js"></script>
    <script src="../../../public/assets/js/calendario.js"></script>
    <script src="../../../public/assets/js/validate.js"></script>
    <script src="../../../public/assets/js/validacoes.js"></script>

    <style>
        input.error {
            color: red;
        }

        .error::placeholder, label.error {
            color: red;
            font-size: 0.9em;
            margin: 0 9px;
        }
    </style>
</head>
<body>
    <?php render_sidebar('operador', 'vendas'); ?>

    <main class="main-content vendas-page vendas-page">
        <h2>Painel de Vendas</h2>
        <?php if ($flash): ?>
            <p style="padding:10px; margin:10px 0; border-radius:6px; background:#f3f5f7;">
                <?php echo e($flash['message']); ?>
            </p>
        <?php endif; ?>
        <hr>
        <form action="../../controller/registar_venda.php" method="POST" id="vendas-form">
            <?php echo csrf_field(); ?>
            <fieldset>
                <legend>Registar venda</legend>
                <table id="produtos-venda">
                    <tr>
                        <td><label for="produto">Produto</label></td>
                        <td><label for="quantidade">Quantidade</label></td>
                        <td><label for="preco">Preço</label></td>
                        <td></td>
                    </tr>
                    <tr class="produto-linha">
                        <td>
                            <select name="produto[]" class="produto">
                                <option value="">Escolha o produto</option>
                                <!-- Produtos serão carregados dinamicamente -->
                            </select>
                        </td>
                        <td><input type="number" name="quantidade[]" class="quantidade" step="1"/></td>
                        <td><input type="number" name="preco[]" class="preco" readonly/></td>
                        <td><button type="button" class="remover-produto">Remover</button></td>
                    </tr>
                </table>
                <button type="button" id="adicionar-produto">Adicionar Produto</button>
                <br><br>
                <button type="submit" id="enviar">Registar</button>
            </fieldset>
        </form>

        <div class="calendario-vendas">
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
    </main>
    <script>
        $(document).ready(function() {
            let produtosInfo = {}; // Objeto para armazenar id e preço dos produtos

            function carregarProdutos() {
                $.ajax({
                    url: '../../api/get_produtos.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            console.log('Erro: ' + response.error);
                        } else {
                            produtosInfo = {}; // Limpa os dados anteriores
                            $('.produto').each(function() {
                                let select = $(this);
                                select.empty().append('<option value="">Selecione um produto</option>');

                                $.each(response, function(index, produto) {
                                    produtosInfo[produto.id_produto] = produto.preco; // Armazena o preço
                                    select.append($('<option>', {
                                        value: produto.id_produto,
                                        text: produto.nome
                                    }));
                                });
                            });
                        }
                    },
                    error: function() {
                        alert('Erro ao carregar produtos.');
                    }
                });
            }

            carregarProdutos();

            // Quando um produto for selecionado, preenche o campo de preço automaticamente
            $(document).on('change', '.produto', function() {
                let idProduto = $(this).val();
                let precoCampo = $(this).closest('tr').find('.preco');
                if (idProduto in produtosInfo) {
                    precoCampo.val(produtosInfo[idProduto]); // Define o preço com base no produto escolhido
                } else {
                    precoCampo.val(''); // Limpa o campo caso o usuário volte a escolher "Selecione um produto"
                }
            });

            // Função para adicionar uma nova linha de produto
            $('#adicionar-produto').click(function() {
                let novaLinha = $('#produtos-venda .produto-linha:first').clone();
                novaLinha.find('select').val('');
                novaLinha.find('input').val('');
                $('#produtos-venda').append(novaLinha);
            });

            // Função para remover uma linha de produto
            $(document).on('click', '.remover-produto', function() {
                if ($('#produtos-venda .produto-linha').length > 1) {
                    $(this).closest('.produto-linha').remove();
                } else {
                    alert('Pelo menos um produto precisa ser adicionado!');
                }
            });
        });
    </script>
</body>
</html>

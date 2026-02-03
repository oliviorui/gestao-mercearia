$(document).ready(function() {
    $.validator.addMethod("somenteLetras", function(valor, elemento) {
        return this.optional(elemento) || /^[A-Za-zÀ-ÿ\s]+$/.test(valor);
    });

    $.validator.addMethod("emailValido", function(valor, elemento) {
        return this.optional(elemento) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor);
    });

    $.validator.addMethod("senhaForte", function(valor, elemento) {
        return this.optional(elemento) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$!%*^?&])[A-Za-z\d@#$!%*&?&]{8,}$/.test(valor);
    });

    // Validação do formulário de login
    $("#login").validate({
        rules: {
            email: {
                required: true,
                email: true,
                emailValido: true
            },
            senha: {
                required: true,
                minlength: 8,
                senhaForte: true
            }
        },
        messages: {
            email: {
                required: "Por favor, insira um e-mail válido.",
                email: "Por favor, insira um e-mail válido.",
                emailValido: "Insira um e-mail válido!"
            },
            senha: {
                required: "Por favor, insira sua senha.",
                minlength: "A senha deve ter pelo menos 8 caracteres.",
                senhaForte: "A senha deve conter maiúsculas, minúsculas, caracteres especiais e números."
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element); // Posiciona a mensagem de erro logo abaixo do campo
        }
    });

    // Validação do formulário de inserção de dados do usuário
    $("#desaparece").validate({
        rules: {
            nome: {
                required: true,
                minlength: 5,
                somenteLetras: true
            },
            email: {
                required: true,
                email: true,
                emailValido: true
            },
            senha: {
                required: true,
                minlength: 8,
                senhaForte: true
            }
        },
        messages: {
            nome: {
                required: "Por favor, insira um nome válido.",
                minlength: "O nome de usuário deve ter pelo menos 5 caracteres.",
                somenteLetras: "Digite apenas letras."
            },
            email: {
                required: "Por favor, insira um e-mail válido.",
                email: "Por favor, insira um e-mail válido.",
                emailValido: "Insira um e-mail válido!"
            },
            senha: {
                required: "Por favor, insira a senha.",
                minlength: "A senha deve ter pelo menos 8 caracteres.",
                senhaForte: "A senha deve conter maiúsculas, minúsculas, caracteres especiais e números."
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element); // Posiciona a mensagem de erro logo abaixo do campo
        }
    });

    // Validação do formulário de edição de dados do usuário
    $("#form-edicao").validate({
        rules: {
            nome: {
                required: true,
                minlength: 5,
                somenteLetras: true
            },
            email: {
                required: true,
                email: true,
                emailValido: true
            }
        },
        messages: {
            nome: {
                required: "Por favor, insira um nome de usuário válido.",
                minlength: "O nome de usuário deve ter pelo menos 5 caracteres.",
                somenteLetras: "Digite apenas letras."
            },
            email: {
                required: "Por favor, insira um e-mail válido.",
                email: "Por favor, insira um e-mail válido.",
                emailValido: "Insira um e-mail válido!"
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element); // Posiciona a mensagem de erro logo abaixo do campo
        }
    });

    // Validação do formulário de inserção de dados do produto
    $("#form-produto").validate({
        rules: {
            nome: {
                required: true,
                minlength: 3
            },
            categoria: {
                required: true
            },
            preco: {
                required: true,
                min: 0
            },
            quant_estoque: {
                required: true,
                min: 6
            },
            descricao: {
                required: true,
                minlength: 2
            }
        },
        messages: {
            nome: {
                required: "Insira o nome de produto.",
                minlength: "Deve ter pelo menos 3 caracteres."
            },
            categoria: {
                required: "Escolha a categoria do produto."
            },
            preco: {
                required: "Defina o preço do produto.",
                min: "Defina um preço de produto válido.",
            },
            quant_estoque: {
                required: "Defina a quantidade em estoque.",
                min: "Mínimo de 6 unidades.",
            },
            descricao: {
                required: "Dê uma descrição do produto.",
                minlength: "Deve ter pelo menos 2 caracteres."
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element); // Posiciona a mensagem de erro logo abaixo do campo
        }
    });

    // Validação do formulário de inserção de dados do produto
    $("#editar_produto").validate({
        rules: {
            nome: {
                required: true,
                minlength: 3
            },
            categoria: {
                required: true
            },
            preco: {
                required: true,
                min: 0
            },
            quantidade_estoque: {
                required: true,
                min: 6
            },
            descricao: {
                required: true,
                minlength: 2
            }
        },
        messages: {
            nome: {
                required: "Por favor, insira o nome de produto.",
                minlength: "Deve ter pelo menos 3 caracteres."
            },
            categoria: {
                required: "Por favor, escolha a categoria."
            },
            preco: {
                required: "Por favor, defina o preço.",
                min: "Por favor, defina um preço válido.",
            },
            quantidade_estoque: {
                required: "Por favor, defina a quantidade em estoque.",
                min: "Mínimo de 6 unidades.",
            },
            descricao: {
                required: "Por favor, dê uma descrição do produto.",
                minlength: "Deve ter pelo menos 2 caracteres."
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element); // Posiciona a mensagem de erro logo abaixo do campo
        }
    });

    // Validação do formulário de registo de vendas
    $("#vendas-form").validate({
        rules: {
            "produto[]": {
                required: true
            },
            "quantidade[]": {
                required: true,
                min: 1,
                max: 99
            }
        },
        messages: {
            "produto[]": {
                required: "Escolha um produto."
            },
            "quantidade[]": {
                required: "Quantidade",
                min: "Mínimo: 1 unidade!",
                max: "Máximo: 99 unidades!"
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element); // Posiciona a mensagem de erro logo abaixo do campo
        }
    });
});
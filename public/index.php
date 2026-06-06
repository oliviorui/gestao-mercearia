<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Banca Mahumane - Sistema de Gerenciamento de Vendas</title>
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="assets/images/logo.png" alt="Logo da Mercearia Mahumane">
            </div>
            <nav>
                <a href="#sobre">Sobre o Sistema</a>
                <a href="#funcionalidades">Funcionalidades</a>
                <a href="#contato">Contato</a>
                <a href="../app/views/login.php">
                    <button class="btn-login">Acessar o Sistema</button>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <section id="sobre" class="intro">
            <div class="container">
                <h1>Bem-vindo ao Sistema de Gerenciamento de Vendas</h1>
                <p>O Sistema de Gerenciamento de Vendas da Mercearia Mahumane foi desenvolvido para otimizar o controle de estoque e o processo de vendas, garantindo mais eficiência no dia a dia da mercearia.</p>
                <p>Com ele, você pode gerenciar produtos, acompanhar as vendas, gerar relatórios e muito mais!</p>
            </div>
        </section>

        <section id="funcionalidades" class="funcionalidades">
            <div class="container">
                <h2>Principais Funcionalidades</h2>
                <div class="feature-cards">
                    <div class="feature-card">
                        <h3>Gestão de Produtos</h3>
                        <p>Cadastro, edição e exclusão de produtos, com controle de preços e quantidade em estoque.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Controle de Vendas</h3>
                        <p>Registro de vendas em tempo real, com atualização automática de estoque e geração de relatórios.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Relatórios Personalizados</h3>
                        <p>Geração de relatórios completos de vendas e movimentação de estoque para ajudar nas decisões da mercearia.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Administração de Usuários</h3>
                        <p>Controle de acessos com diferentes permissões para administradores e vendedores.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="contato" class="contato">
            <div class="container">
                <h2>Entre em Contato</h2>
                <p>Se tiver dúvidas sobre o sistema ou precisar de suporte, entre em contato conosco pelo e-mail:</p>
                <p><a href="mailto:contato@merceariamahumane.com.br">contato@merceariamahumane.com.br</a></p>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Mercearia Mahumane. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>

</html>

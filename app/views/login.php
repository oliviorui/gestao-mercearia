<?php
require_once __DIR__ . '/../helpers/security.php';

if (is_authenticated()) {
    $tipo = $_SESSION['usuario']['tipo_usuario'] ?? '';
    $redirectPage = ($tipo === 'admin') ? "admin/geral.php" : "operador/vendas.php";
    header("Location: " . app_base_path() . "/app/views/{$redirectPage}");
    exit();
}

$flash = flash_get();
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Banca Mahumane - Login</title>
    <link rel="icon" href="../../public/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../public/assets/css/auth.css">
    
    <script src="../../public/assets/js/jquery.js"></script>
    <script src="../../public/assets/js/validate.js"></script>
    <script src="../../public/assets/js/validacoes.js"></script>

    <style>
        input.error {
            border-bottom: 2px solid red;
            color: red;
        }

        .error::placeholder, label.error {
            color: red;
        }
    </style>
</head>
<body>
    <main>
        <div>
            <h1>Entre e conecte-se com a tecnologia!</h1>
            <p>
                A tecnologia está sempre em movimento, e estamos aqui para garantir que você acompanhe o ritmo. Faça login na sua conta e gerencie as suas vendas como deve ser
            </p>
        </div>

        <form action="../controller/autenticar.php" method="POST" id="login">
            <input type="hidden" name="acao" value="login">
            <?php echo csrf_field(); ?>
            <?php if ($flash): ?><p style="color:red; text-align:center;"><?php echo e($flash['message']); ?></p><?php endif; ?>
            <label>
                <input type="email" class="campos" placeholder="E-mail" id="email" name="email">
            </label>

            <label>
                <input type="password" class="campos" placeholder="Senha" id="senha" name="senha">
            </label>

            <button type="submit">Entrar</button>
            <p>Não tem uma conta? <a href="mailto:oliviorui@gmail.com">Clique aqui</a></p>
        </form>
    </main>
</body>
</html>

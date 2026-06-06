<?php
function render_sidebar(string $area, string $active): void {
    $nomeUsuario = $_SESSION['usuario']['nome'] ?? 'Usuário';

    if ($area === 'admin') {
        $menu = [
            'geral' => ['label' => 'Geral', 'href' => 'geral.php'],
            'produtos' => ['label' => 'Cadastrar Produtos', 'href' => 'produtos.php'],
            'estoque' => ['label' => 'Consultar Estoque', 'href' => 'estoque.php'],
            'usuarios' => ['label' => 'Gerenciar Usuários', 'href' => 'usuarios.php'],
            'logs' => ['label' => 'Atividades no Sistema', 'href' => 'logs.php'],
        ];
    } else {
        $menu = [
            'vendas' => ['label' => 'Painel de Vendas', 'href' => 'vendas.php'],
            'estoque' => ['label' => 'Consultar Estoque', 'href' => 'estoque.php'],
        ];
    }
    ?>
    <aside class="sidebar">
        <div class="perfil">
            <img src="../../../public/assets/images/admin.png" alt="Usuário">
            <p><?php echo e($nomeUsuario); ?></p>
        </div>
        <hr>
        <nav>
            <?php foreach ($menu as $key => $item): ?>
                <a href="<?php echo e($item['href']); ?>" class="<?php echo $active === $key ? 'atual' : ''; ?>">
                    <?php echo e($item['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <form action="../../controller/autenticar.php" method="POST" id="logout">
            <input type="hidden" name="acao" value="logout">
            <?php echo csrf_field(); ?>
            <input type="image" src="../../../public/assets/images/icons/logout_24dp.svg" alt="Terminar sessão" id="img-logout" title="Terminar Sessão">
        </form>
    </aside>
    <?php
}

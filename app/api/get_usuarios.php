<?php
require_once '../config/database.php';
session_start();  // Inicia a sessão

header('Content-Type: application/json');

// Verificar se o usuário está logado (por exemplo, verificando uma variável de sessão)
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']['id_usuario'])) {
    echo json_encode(['error' => 'Usuário não está logado.']);
    exit;  // Encerra a execução da API se o usuário não estiver logado
}

// Verifica se o usuário logado é do tipo 'admin'
if ($_SESSION['usuario']['tipo_usuario'] !== 'admin') {
    echo json_encode(['error' => 'Acesso restrito. Somente administradores podem acessar esses dados.']);
    exit;  // Encerra a execução se o usuário não for admin
}

try {
    $database = new Database();
    $conn = $database->conectar();

    // Consulta SQL para obter todos os usuarios
    $sql_usuarios = "SELECT id_usuario, nome, email, tipo_usuario, data_cadastro FROM usuarios";
    $sql_usuarios = $conn->prepare($sql_usuarios);
    $sql_usuarios->execute();

    // Recuperar todos os usuarios
    $usuarios = $sql_usuarios->fetchAll(PDO::FETCH_ASSOC);

    // Verificar se há usuarios
    if (count($usuarios) > 0) {
        echo json_encode($usuarios);
    } else {
        echo json_encode(['message' => 'Nenhum usuario encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao obter usuarios: ' . $e->getMessage()]);
}
?>

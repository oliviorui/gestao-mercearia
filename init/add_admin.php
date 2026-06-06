<?php
$host = 'localhost';
$dbname = 'sistema_vendas';
$username = 'root'; 
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nome = 'Olívio Cumbe';
    $email = 'oliviocumbe@email.com';
    $senha = password_hash('#SenhaAdmin123', PASSWORD_BCRYPT);
    $tipo = 'admin';
    $data_cds = date("Y-m-d H:i:s");

    $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, data_cadastro) VALUES (:nome, :email, :senha, :tipo, :data_cds)";
    $stmt = $pdo->prepare($sql);

    // Bind dos parâmetros
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':data_cds', $data_cds);  // Adicionando este bind

    // Execução da query
    if ($stmt->execute()) {
        echo "Usuário inserido com sucesso!";
    } else {
        echo "Erro ao inserir usuário.";
    }
} catch (PDOException $e) {
    // Exibe o erro caso ocorra
    echo "Erro de conexão ou execução: " . $e->getMessage();
}
?>

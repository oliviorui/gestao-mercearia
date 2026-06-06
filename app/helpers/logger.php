<?php
function registrar_log(PDO $conn, $idUsuario, $tipo, $descricao) {
    try {
        $sql = "INSERT INTO logs (id_usuario, data_hora, tipo_actividade, descricao)
                VALUES (:id_usuario, NOW(), :tipo, :descricao)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_usuario', (int) $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log('Erro ao registrar log: ' . $e->getMessage());
    }
}

<?php
require_once '../config/database.php';

class CrudUsuario {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }
    // Função para registrar logs
    private function registrarLog($idUsuario, $tipo, $descricao) {
        try {
            $sql = "INSERT INTO logs (id_usuario, data_hora, tipo_actividade, descricao) 
                    VALUES (:id_usuario, NOW(), :tipo, :descricao)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao registrar log: " . $e->getMessage());
        }
    }

    public function cadastrarUsuario($nome, $email, $senha, $tipo_usuario) {
        try {
            $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
            $dataCadastro = date('Y-m-d H:i:s');  // Formato de data e hora padrão do MySQL
    
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, data_cadastro) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$nome, $email, $hashSenha, $tipo_usuario, $dataCadastro]);
    
            // Recupera o ID do usuário recém-criado
            $idUsuario = $this->conn->lastInsertId();
            
            // Registrar log de cadastro
            $this->registrarLog($idUsuario, 'Cadastro de usuário', 'Usuário cadastrado no sistema.');
            
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar usuário: " . $e->getMessage());
            return false;
        }
    }
    

    public function editarUsuario($id, $nome, $email, $tipo_usuario, $senha = null) {
        try {
            if (!empty($senha)) {
                $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo_usuario = ?, senha = ? WHERE id_usuario = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$nome, $email, $tipo_usuario, $hashSenha, $id]);
            } else {
                $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo_usuario = ? WHERE id_usuario = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$nome, $email, $tipo_usuario, $id]);
            }

            // Registrar log de edição
            $this->registrarLog($id, 'Edição de usuário', 'Dados do usuário editado no sistema.');
            
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao editar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function excluirUsuario($id) {
        try {
            $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
    
            // Registrar log de exclusão
            $this->registrarLog($id, 'Exclusão de usuário', 'Usuário excluído do sistema.');
            
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            return false;
        }
    }       
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $crud = new CrudUsuario();

    if (isset($_POST['acao'])) {
        $acao = $_POST['acao'];

        if ($acao == 'cadastrar') {
            $crud->cadastrarUsuario($_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['tipo_usuario']);
            header("Location: ../views/admin/usuarios.php");
        } elseif ($acao == 'editar') {
            $senha = !empty($_POST['senha']) ? $_POST['senha'] : null;
            $crud->editarUsuario($_POST['id_usuario'], $_POST['nome'], $_POST['email'], $_POST['tipo_usuario'], $senha);
            header("Location: ../views/admin/usuarios.php");
        } elseif ($acao == 'excluir') {
            $crud->excluirUsuario($_POST['id_usuario']);
            header("Location: ../views/admin/usuarios.php");
        }
    }
}
?>

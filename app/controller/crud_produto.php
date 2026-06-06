<?php
require_once '../config/database.php';

// Iniciar a sessão
session_start();

// Verificar se o usuário está logado e é um admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

class CrudProduto {
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

    public function cadastrarProduto($nome, $categoria, $preco, $quantidade_estoque, $descricao) {
        try {
            $idUsuario = $_SESSION['usuario']['id_usuario']; // ID do usuário logado
            
            $sql = "INSERT INTO produtos (nome, categoria, preco, quantidade_estoque, descricao) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$nome, $categoria, $preco, $quantidade_estoque, $descricao]);

            // Registrar log de cadastro
            $this->registrarLog($idUsuario, 'Cadastro de produto', 'Produto cadastrado no sistema.');

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar produto: " . $e->getMessage());
            return false;
        }
    }

    public function editarProduto($id, $nome, $categoria, $preco, $quantidade_estoque, $descricao) {
        try {
            $idUsuario = $_SESSION['usuario']['id_usuario']; // ID do usuário logado

            $sql = "UPDATE produtos SET nome = ?, categoria = ?, preco = ?, quantidade_estoque = ?, descricao = ? WHERE id_produto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$nome, $categoria, $preco, $quantidade_estoque, $descricao, $id]);

            // Registrar log de edição
            $this->registrarLog($idUsuario, 'Edição de produto', 'Produto editado no sistema.');

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao editar produto: " . $e->getMessage());
            return false;
        }
    }

    public function deletarProduto($id) {
        try {
            $idUsuario = $_SESSION['usuario']['id_usuario']; // ID do usuário logado

            $sql = "DELETE FROM produtos WHERE id_produto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            // Registrar log de deleção
            $this->registrarLog($idUsuario, 'Exclusão de produto', 'Produto excluído do sistema.');

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao deletar produto: " . $e->getMessage());
            return false;
        }
    }
}

// Lógica de cadastro, edição ou exclusão
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $crud = new CrudProduto();

    if (isset($_POST['acao'])) {
        $acao = $_POST['acao'];

        // Recuperar ID do usuário logado
        $idUsuario = $_SESSION['usuario']['id_usuario']; 

        if ($acao == 'cadastrar') {
            $crud->cadastrarProduto($_POST['nome'], $_POST['categoria'], $_POST['preco'], $_POST['quant_estoque'], $_POST['descricao']);
            header("Location: ../views/admin/produtos.php");
        } elseif ($acao == 'editar') {
            $crud->editarProduto($_POST['id_produto'], $_POST['nome'], $_POST['categoria'], $_POST['preco'], $_POST['quantidade_estoque'], $_POST['descricao']);
            header("Location: ../views/admin/produtos.php");
        } elseif ($acao == 'deletar') {
            $crud->deletarProduto($_POST['id_produto']);
            header("Location: ../views/admin/produtos.php");
        }
    }
}
?>

<?php
require_once __DIR__ . '/../helpers/security.php';
require_auth('admin', '../views/login.php');

class CrudProduto {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    private function registrarLog($tipo, $descricao): void {
        try {
            $idUsuario = $_SESSION['usuario']['id_usuario'];
            $sql = "INSERT INTO logs (id_usuario, data_hora, tipo_actividade, descricao)
                    VALUES (:id_usuario, NOW(), :tipo, :descricao)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_usuario' => $idUsuario,
                ':tipo' => $tipo,
                ':descricao' => $descricao,
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao registrar log: " . $e->getMessage());
        }
    }

    private function validarProduto($nome, $categoria, $preco, $quantidade): bool {
        return strlen(trim((string)$nome)) >= 3
            && trim((string)$categoria) !== ''
            && is_numeric($preco)
            && (float)$preco > 0
            && is_numeric($quantidade)
            && (int)$quantidade >= 0;
    }

    public function cadastrarProduto($nome, $categoria, $preco, $quantidade_estoque, $descricao): bool {
        if (!$this->validarProduto($nome, $categoria, $preco, $quantidade_estoque)) {
            return false;
        }

        try {
            $sql = "INSERT INTO produtos (nome, categoria, preco, quantidade_estoque, descricao)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                trim($nome),
                trim($categoria),
                (float)$preco,
                (int)$quantidade_estoque,
                trim((string)$descricao),
            ]);

            $this->registrarLog('Cadastro de produto', 'Produto cadastrado no sistema: ' . trim($nome));
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar produto: " . $e->getMessage());
            return false;
        }
    }

    public function editarProduto($id, $nome, $categoria, $preco, $quantidade_estoque, $descricao): bool {
        if ((int)$id <= 0 || !$this->validarProduto($nome, $categoria, $preco, $quantidade_estoque)) {
            return false;
        }

        try {
            $sql = "UPDATE produtos
                    SET nome = ?, categoria = ?, preco = ?, quantidade_estoque = ?, descricao = ?
                    WHERE id_produto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                trim($nome),
                trim($categoria),
                (float)$preco,
                (int)$quantidade_estoque,
                trim((string)$descricao),
                (int)$id,
            ]);

            $this->registrarLog('Edição de produto', 'Produto editado no sistema: ID ' . (int)$id);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao editar produto: " . $e->getMessage());
            return false;
        }
    }

    public function deletarProduto($id): bool {
        if ((int)$id <= 0) return false;

        try {
            $stmt = $this->conn->prepare("DELETE FROM produtos WHERE id_produto = ?");
            $stmt->execute([(int)$id]);

            $this->registrarLog('Exclusão de produto', 'Produto excluído do sistema: ID ' . (int)$id);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao deletar produto: " . $e->getMessage());
            return false;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $crud = new CrudProduto();
    $acao = $_POST['acao'] ?? '';
    $ok = false;

    if ($acao === 'cadastrar') {
        $ok = $crud->cadastrarProduto($_POST['nome'] ?? '', $_POST['categoria'] ?? '', $_POST['preco'] ?? 0, $_POST['quant_estoque'] ?? 0, $_POST['descricao'] ?? '');
    } elseif ($acao === 'editar') {
        $ok = $crud->editarProduto($_POST['id_produto'] ?? 0, $_POST['nome'] ?? '', $_POST['categoria'] ?? '', $_POST['preco'] ?? 0, $_POST['quantidade_estoque'] ?? 0, $_POST['descricao'] ?? '');
    } elseif ($acao === 'deletar') {
        $ok = $crud->deletarProduto($_POST['id_produto'] ?? 0);
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $ok]);
        exit();
    }

    header("Location: ../views/admin/produtos.php");
    exit();
}
?>

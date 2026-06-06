<?php
require_once __DIR__ . '/../helpers/security.php';
require_auth('admin', '../views/login.php');

class CrudUsuario {
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

    private function emailExiste($email, $ignorarId = null): bool {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        $params = [':email' => trim($email)];

        if ($ignorarId !== null) {
            $sql .= " AND id_usuario <> :id";
            $params[':id'] = (int)$ignorarId;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function validarDados($nome, $email, $tipo_usuario): bool {
        return strlen(trim((string)$nome)) >= 3
            && filter_var(trim((string)$email), FILTER_VALIDATE_EMAIL)
            && in_array($tipo_usuario, ['admin', 'operador'], true);
    }

    public function cadastrarUsuario($nome, $email, $senha, $tipo_usuario): bool {
        $nome = trim((string)$nome);
        $email = trim((string)$email);

        if (!$this->validarDados($nome, $email, $tipo_usuario) || strlen((string)$senha) < 4 || $this->emailExiste($email)) {
            return false;
        }

        try {
            $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, data_cadastro)
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$nome, $email, $hashSenha, $tipo_usuario]);

            $this->registrarLog('Cadastro de usuário', 'Usuário cadastrado no sistema: ' . $nome);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function editarUsuario($id, $nome, $email, $tipo_usuario, $senha = null): bool {
        $id = (int)$id;
        $nome = trim((string)$nome);
        $email = trim((string)$email);

        if ($id <= 0 || !$this->validarDados($nome, $email, $tipo_usuario) || $this->emailExiste($email, $id)) {
            return false;
        }

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

            $this->registrarLog('Edição de usuário', 'Dados do usuário editados no sistema: ID ' . $id);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao editar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function excluirUsuario($id): bool {
        $id = (int)$id;
        if ($id <= 0) return false;

        try {
            $stmtTipo = $this->conn->prepare("SELECT tipo_usuario FROM usuarios WHERE id_usuario = ?");
            $stmtTipo->execute([$id]);
            $tipo = $stmtTipo->fetchColumn();

            if ($tipo === 'admin') {
                $totalAdmins = (int)$this->conn->query("SELECT COUNT(*) FROM usuarios WHERE tipo_usuario = 'admin'")->fetchColumn();
                if ($totalAdmins <= 1) {
                    return false;
                }
            }

            if ($id === (int)($_SESSION['usuario']['id_usuario'] ?? 0)) {
                return false;
            }

            $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id]);

            $this->registrarLog('Exclusão de usuário', 'Usuário excluído do sistema: ID ' . $id);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            return false;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $crud = new CrudUsuario();
    $acao = $_POST['acao'] ?? '';
    $ok = false;

    if ($acao === 'cadastrar') {
        $ok = $crud->cadastrarUsuario($_POST['nome'] ?? '', $_POST['email'] ?? '', $_POST['senha'] ?? '', $_POST['tipo_usuario'] ?? '');
    } elseif ($acao === 'editar') {
        $senha = !empty($_POST['senha']) ? $_POST['senha'] : null;
        $ok = $crud->editarUsuario($_POST['id_usuario'] ?? 0, $_POST['nome'] ?? '', $_POST['email'] ?? '', $_POST['tipo_usuario'] ?? '', $senha);
    } elseif ($acao === 'excluir') {
        $ok = $crud->excluirUsuario($_POST['id_usuario'] ?? 0);
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $ok]);
        exit();
    }

    header("Location: ../views/admin/usuarios.php");
    exit();
}
?>

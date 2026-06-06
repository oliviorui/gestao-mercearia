<?php
require_once __DIR__ . '/../helpers/security.php';

class Autenticacao {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    private function gerarToken(): string {
        return bin2hex(random_bytes(32));
    }

    private function registrarLog($idUsuario, $tipo, $descricao): void {
        if (!$this->conn) return;
        try {
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

    public function autenticarUsuario($email, $senha) {
        try {
            if (!$this->conn) return false;

            $email = trim((string)$email);
            $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify((string)$senha, $usuario['senha'])) {
                session_regenerate_id(true);
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['usuario'] = $usuario;

                $token = $this->gerarToken();
                set_app_cookie('token_usuario', $token, time() + (30 * 24 * 60 * 60));
                set_app_cookie('token_ususario', $token, time() + (30 * 24 * 60 * 60)); // compatibilidade com versões antigas
                set_app_cookie('id_usuario', (string)$usuario['id_usuario'], time() + (30 * 24 * 60 * 60));
                set_app_cookie('status', 'ativo', time() + (30 * 24 * 60 * 60));

                $sqlSessao = "INSERT INTO sessoes (id_usuario, token, data_expiracao, estado)
                              VALUES (:id_usuario, :token, DATE_ADD(NOW(), INTERVAL 1 MONTH), 'ativa')";
                $stmtSessao = $this->conn->prepare($sqlSessao);
                $stmtSessao->execute([
                    ':id_usuario' => $usuario['id_usuario'],
                    ':token' => $token,
                ]);

                $this->registrarLog($usuario['id_usuario'], 'Login', 'Usuário logou no sistema.');
                return $usuario;
            }

            if ($usuario) {
                $this->registrarLog($usuario['id_usuario'], 'Login falhado', 'Tentativa de login com senha incorreta.');
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro na autenticação: " . $e->getMessage());
            return false;
        }
    }

    public function verificarSessao(): bool {
        return restore_session_from_cookie();
    }

    public function logout(): void {
        if (isset($_SESSION['id_usuario'])) {
            $this->registrarLog($_SESSION['id_usuario'], 'Logout', 'Usuário saiu do sistema.');
        }

        foreach (['token_usuario', 'token_ususario'] as $cookieName) {
            if (!empty($_COOKIE[$cookieName]) && $this->conn) {
                try {
                    $stmt = $this->conn->prepare("UPDATE sessoes SET estado = 'desativada' WHERE token = :token");
                    $stmt->execute([':token' => $_COOKIE[$cookieName]]);
                } catch (PDOException $e) {
                    error_log("Erro ao terminar sessão: " . $e->getMessage());
                }
            }
            clear_app_cookie($cookieName);
        }

        clear_app_cookie('id_usuario');
        clear_app_cookie('status');

        session_unset();
        session_destroy();

        header("Location: ../views/login.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $auth = new Autenticacao();

    switch ($acao) {
        case 'login':
            require_valid_csrf();
            $usuario = $auth->autenticarUsuario($_POST['email'] ?? '', $_POST['senha'] ?? '');

            if ($usuario) {
                $redirectPage = ($usuario['tipo_usuario'] === 'admin') ? "admin/geral.php" : "operador/vendas.php";
                header("Location: " . app_base_path() . "/app/views/{$redirectPage}");
            } else {
                flash_set('erro', 'Email ou senha inválidos.');
                header("Location: " . app_base_path() . "/app/views/login.php");
            }
            exit();

        case 'logout':
            require_valid_csrf();
            $auth->logout();
            exit();
    }
}
?>

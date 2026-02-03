<?php
require_once '../config/database.php';

class Autenticacao {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    private function gerarToken() {
        return bin2hex(random_bytes(32));
    }

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

    public function autenticarUsuario($email, $senha) {
        try {
            session_start();
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['usuario'] = $usuario;

                // Gerar token de sessão e armazenar
                $token = $this->gerarToken();
                setcookie("token_ususario", $token, time()+(30*24*60*60), "/", "", true, true);

                // Definir cookies para o id_usuario e status da sessão
                setcookie('id_usuario', $usuario['id_usuario'], time() + (30 * 24 * 60 * 60), "/", "", false, true);
                setcookie('status', 'ativo', time() + (30 * 24 * 60 * 60), "/", "", false, true);

                
                $sqlSessao = "INSERT INTO sessoes (id_usuario, token, data_expiracao, estado) VALUES (:id_usuario, :token, DATE_ADD(NOW(), INTERVAL 1 MONTH), 'ativa')";
                $stmtSessao = $this->conn->prepare($sqlSessao);
                $stmtSessao->bindParam(':id_usuario', $usuario['id_usuario'], PDO::PARAM_INT);
                $stmtSessao->bindParam(':token', $token, PDO::PARAM_STR);
                $stmtSessao->execute();
                
                $this->registrarLog($usuario['id_usuario'], 'Login', 'Usuário logou no sistema.');
                return $usuario;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro na autenticação: " . $e->getMessage());
            return false;
        }
    }

    public function verificarSessao() {
        if (isset($_COOKIE['token_ususario'])) {
            $token = $_COOKIE['token_ususario'];
            
            $sql = "SELECT u.* FROM sessoes s JOIN usuarios u ON s.id_usuario = u.id_usuario WHERE s.token = :token AND s.data_expiracao > NOW() AND s.estado = 'ativa'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['usuario'] = $usuario;
                return true;
            }
        }
        return false;
    }

    public function logout() {
        session_start();
        if (isset($_SESSION['id_usuario'])) {
            $idUsuario = $_SESSION['id_usuario'];
            $this->registrarLog($idUsuario, 'Logout', 'Usuário saiu do sistema.');
        }

        if (isset($_COOKIE['token_ususario'])) {
            $token = $_COOKIE['token_ususario'];
            setcookie("token_ususario", "", time() - 3600, "/");
            
            $sql = "UPDATE sessoes SET estado = 'desativada' WHERE token = :token";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
        }

        if (isset($_COOKIE['id_usuario'])) {
            setcookie('id_usuario', '', time() - 3600, "/"); // Expira no passado
        }
        
        if (isset($_COOKIE['status'])) {
            setcookie('status', '', time() - 3600, "/"); // Expira no passado
        }

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
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $usuario = $auth->autenticarUsuario($email, $senha);

            if ($usuario) {
                $redirectPage = $usuario['tipo_usuario'] === 'admin' ? "admin/geral.php" : "operador/vendas.php";
                header("Location: /mercearia/app/views/$redirectPage");
            } else {
                header("Location: /mercearia/app/views/login.php");
            }
            exit;

        case 'logout':
            $auth->logout();
            exit;
    }
}

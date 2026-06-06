<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function app_base_path(): string {
    return '/gestao-mercearia';
}

function app_url(string $path = ''): string {
    return rtrim(app_base_path(), '/') . '/' . ltrim($path, '/');
}

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function h($value): string {
    return e($value);
}

function is_https_request(): bool {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
}

function set_app_cookie(string $name, string $value, int $expires): void {
    setcookie($name, $value, [
        'expires' => $expires,
        'path' => '/',
        'secure' => is_https_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clear_app_cookie(string $name): void {
    setcookie($name, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => is_https_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function restore_session_from_cookie(): bool {
    if (!empty($_SESSION['usuario']['id_usuario'])) {
        return true;
    }

    $token = $_COOKIE['token_usuario'] ?? ($_COOKIE['token_ususario'] ?? null);
    if (!$token) {
        return false;
    }

    try {
        $database = new Database();
        $conn = $database->conectar();
        if (!$conn) {
            return false;
        }

        $sql = "SELECT u.*
                FROM sessoes s
                INNER JOIN usuarios u ON s.id_usuario = u.id_usuario
                WHERE s.token = :token
                  AND s.data_expiracao > NOW()
                  AND s.estado = 'ativa'
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':token' => $token]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['usuario'] = $usuario;
            return true;
        }
    } catch (Throwable $e) {
        error_log('Erro ao restaurar sessão: ' . $e->getMessage());
    }

    return false;
}

function is_authenticated(): bool {
    return restore_session_from_cookie();
}

function user_has_role($roles): bool {
    if (!is_authenticated()) {
        return false;
    }

    $roles = is_array($roles) ? $roles : [$roles];
    return in_array($_SESSION['usuario']['tipo_usuario'] ?? '', $roles, true);
}

function require_auth($roles = null, string $redirect = '../login.php'): void {
    if (!is_authenticated()) {
        header("Location: {$redirect}");
        exit();
    }

    if ($roles !== null && !user_has_role($roles)) {
        header("Location: {$redirect}");
        exit();
    }
}

function require_role($roles, string $redirect = '../login.php'): void {
    require_auth($roles, $redirect);
}

function require_api_auth($roles = null): void {
    header('Content-Type: application/json; charset=utf-8');

    if (!is_authenticated()) {
        http_response_code(401);
        echo json_encode(['error' => 'Usuário não está logado.']);
        exit();
    }

    if ($roles !== null && !user_has_role($roles)) {
        http_response_code(403);
        echo json_encode(['error' => 'Acesso restrito.']);
        exit();
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_input(): string {
    return csrf_field();
}

function verify_csrf_token(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function require_valid_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verify_csrf_token()) {
        http_response_code(403);
        exit('Token CSRF inválido. Atualize a página e tente novamente.');
    }
}

function flash_set(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array {
    if (empty($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

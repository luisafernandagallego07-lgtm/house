<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 8 * 3600);
    session_set_cookie_params([
        'lifetime' => 8 * 3600,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

define('SESSION_IDLE_LIMIT', 15 * 60); // 15 min

function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    // Cierre por inactividad (deslizante)
    if (!empty($_SESSION['last_activity']) &&
        (time() - $_SESSION['last_activity']) > SESSION_IDLE_LIMIT) {
        session_unset();
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
    $_SESSION['last_activity'] = time();

    // Renovación deslizante del id de sesión cada 30 min
    if (!isset($_SESSION['created'])) $_SESSION['created'] = time();
    if (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

function require_admin(): void {
    require_login();
    if (($_SESSION['rol'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Acceso restringido');
    }
}

function current_user(): array {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'nombre' => $_SESSION['nombre'] ?? '',
        'rol' => $_SESSION['rol'] ?? '',
    ];
}

function log_modulo(string $modulo): void {
    try {
        $stmt = db()->prepare('INSERT INTO log_uso_modulos (usuario_id, modulo) VALUES (?,?)');
        $stmt->execute([$_SESSION['user_id'] ?? 0, $modulo]);
    } catch (Throwable $e) {}
}

function log_error(string $msg): void {
    try {
        $stmt = db()->prepare('INSERT INTO log_errores (mensaje, usuario_id) VALUES (?,?)');
        $stmt->execute([$msg, $_SESSION['user_id'] ?? null]);
    } catch (Throwable $e) {}
}
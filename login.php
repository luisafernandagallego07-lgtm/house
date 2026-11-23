<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = 'Ingresa tu correo y contraseña.';
    } else {
        $stmt = db()->prepare('SELECT * FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1');
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if (!$u) {
            $error = 'Credenciales inválidas.';
        } elseif ($u['bloqueado_hasta'] && strtotime($u['bloqueado_hasta']) > time()) {
            $error = 'Cuenta bloqueada temporalmente. Intenta más tarde.';
        } elseif (!password_verify($pass, $u['password_hash'])) {
            $fallos = (int)$u['intentos_fallidos'] + 1;
            $bloqueo = $fallos >= 5 ? date('Y-m-d H:i:s', time() + 900) : null;
            $up = db()->prepare('UPDATE usuarios SET intentos_fallidos=?, bloqueado_hasta=? WHERE id=?');
            $up->execute([$fallos, $bloqueo, $u['id']]);
            $error = 'Credenciales inválidas.';
        } else {
            // Login exitoso
            session_regenerate_id(true);
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['nombre']  = $u['nombre'];
            $_SESSION['rol']     = $u['rol'];
            $_SESSION['last_activity'] = time();
            $_SESSION['created'] = time();

            db()->prepare('UPDATE usuarios SET intentos_fallidos=0, bloqueado_hasta=NULL, ultimo_acceso=NOW() WHERE id=?')
                ->execute([$u['id']]);

            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>STOCKLY — Iniciar sesión</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">
  <main class="auth-card" role="main">
    <h1>STOCKLY</h1>
    <p class="muted">Gestión de inventario</p>
    <?php if (!empty($_GET['expired'])): ?>
      <div class="banner banner-warn">Sesión cerrada por inactividad.</div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="banner banner-error" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <label for="email">Correo</label>
      <input id="email" name="email" type="email" required autocomplete="username">
      <label for="password">Contraseña</label>
      <input id="password" name="password" type="password" required autocomplete="current-password">
      <button type="submit" class="btn btn-primary btn-block">Entrar</button>
    </form>
    <p class="muted small">Usuario demo: <code>admin@stockly.local</code> / <code>Admin123!</code></p>
  </main>
</body>
</html>
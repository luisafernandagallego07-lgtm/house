<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_admin();
log_modulo('usuarios');
$page = 'Usuarios';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $rol = $_POST['rol'] === 'admin' ? 'admin' : 'editor';

    if ($nombre && $email && strlen($pass) >= 8 && preg_match('/[A-Z]/',$pass) && preg_match('/[a-z]/',$pass) && preg_match('/[0-9]/',$pass)) {
        try {
            db()->prepare('INSERT INTO usuarios (nombre,email,password_hash,rol) VALUES (?,?,?,?)')
               ->execute([$nombre, $email, password_hash($pass, PASSWORD_DEFAULT), $rol]);
            $msg = 'Usuario creado.';
        } catch (Throwable $e) {
            $msg = 'Error: email duplicado o inválido.';
        }
    } else {
        $msg = 'Contraseña debe tener 8+ caracteres, mayúscula, minúscula y número.';
    }
}
if (($_GET['toggle'] ?? '') !== '') {
    db()->prepare('UPDATE usuarios SET activo = 1 - activo WHERE id=?')->execute([(int)$_GET['toggle']]);
    header('Location: usuarios.php'); exit;
}

$users = db()->query('SELECT id,nombre,email,rol,activo,ultimo_acceso FROM usuarios ORDER BY id')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<h1>Usuarios</h1>
<?php if ($msg): ?><div class="banner banner-ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="grid-2">
  <form method="post" class="card">
    <h2>Nuevo usuario</h2>
    <label>Nombre<input name="nombre" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Contraseña<input type="password" name="password" required minlength="8"></label>
    <label>Rol
      <select name="rol"><option value="editor">Editor</option><option value="admin">Admin</option></select>
    </label>
    <button class="btn btn-primary">Crear</button>
  </form>
  <div class="card">
    <h2>Existentes</h2>
    <table class="table">
      <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['nombre']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge"><?= $u['rol'] ?></span></td>
          <td><?= $u['activo']?'<span class="badge badge-success">Activo</span>':'<span class="badge badge-danger">Inactivo</span>' ?></td>
          <td><a href="?toggle=<?= $u['id'] ?>" class="btn btn-sm"><?= $u['activo']?'Desactivar':'Activar' ?></a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
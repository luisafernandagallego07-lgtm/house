<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
log_modulo('proveedores');
$page = 'Proveedores';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        trim($_POST['nombre'] ?? ''),
        trim($_POST['contacto'] ?? ''),
        trim($_POST['telefono'] ?? ''),
        trim($_POST['email'] ?? ''),
        trim($_POST['direccion'] ?? ''),
    ];
    if ($data[0] !== '') {
        if ($id) {
            db()->prepare('UPDATE proveedores SET nombre=?,contacto=?,telefono=?,email=?,direccion=? WHERE id=?')
                ->execute([...$data, $id]);
        } else {
            db()->prepare('INSERT INTO proveedores (nombre,contacto,telefono,email,direccion) VALUES (?,?,?,?,?)')
                ->execute($data);
        }
    }
    header('Location: proveedores.php');
    exit;
}
if (($_GET['delete'] ?? '') !== '') {
    db()->prepare('DELETE FROM proveedores WHERE id=?')->execute([(int)$_GET['delete']]);
    header('Location: proveedores.php');
    exit;
}
$items = db()->query('SELECT * FROM proveedores ORDER BY nombre')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<h1>Proveedores</h1>
<div class="grid-2">
  <form method="post" class="card">
    <h2>Nuevo proveedor</h2>
    <label>Nombre *<input name="nombre" required></label>
    <label>Contacto<input name="contacto"></label>
    <label>Teléfono<input name="telefono"></label>
    <label>Email<input name="email" type="email"></label>
    <label>Dirección<input name="direccion"></label>
    <button class="btn btn-primary">Agregar</button>
  </form>
  <div class="card">
    <h2>Existentes</h2>
    <table class="table">
      <thead><tr><th>Nombre</th><th>Contacto</th><th>Tel</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['nombre']) ?></td>
          <td><?= htmlspecialchars($it['contacto']) ?></td>
          <td><?= htmlspecialchars($it['telefono']) ?></td>
          <td><a href="?delete=<?= $it['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
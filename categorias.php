<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
log_modulo('categorias');
$page = 'Categorías';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $desc = trim($_POST['descripcion'] ?? '');
    
    if ($nombre !== '') {
        try {
            if ($id) {
                db()->prepare('UPDATE categorias SET nombre=?, descripcion=? WHERE id=?')->execute([$nombre,$desc,$id]);
            } else {
                db()->prepare('INSERT INTO categorias (nombre, descripcion) VALUES (?,?)')->execute([$nombre,$desc]);
            }
            $_SESSION['flash'] = 'Categoría guardada correctamente.';
        } catch (PDOException $e) {
            // Si el error es porque la categoría ya existe (código 1062 de MySQL)
            if ($e->errorInfo[1] == 1062) {
                $_SESSION['flash_error'] = 'La categoría "' . htmlspecialchars($nombre) . '" ya existe. Intenta con otro nombre.';
            } else {
                $_SESSION['flash_error'] = 'Ocurrió un error al guardar la categoría.';
            }
        }
    }
    header('Location: categorias.php');
    exit;
}

// Eliminar categoría
if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    try {
        db()->prepare('DELETE FROM categorias WHERE id=?')->execute([(int)$_GET['delete']]);
        $_SESSION['flash'] = 'Categoría eliminada.';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'No se pudo eliminar la categoría (puede tener productos asociados).';
    }
    header('Location: categorias.php');
    exit;
}

$items = db()->query('SELECT c.*, (SELECT COUNT(*) FROM productos p WHERE p.categoria_id=c.id) total FROM categorias c ORDER BY nombre')->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<h1>Categorías</h1>

<!-- Mensajes de éxito o error -->
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="banner banner-ok"><?= htmlspecialchars($_SESSION['flash']) ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="banner banner-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="grid-2">
  <form method="post" class="card">
    <h2>Nueva categoría</h2>
    <label>Nombre *<input name="nombre" required></label>
    <label>Descripción<input name="descripcion"></label>
    <button class="btn btn-primary">Agregar</button>
  </form>
  
  <div class="card">
    <h2>Existentes</h2>
    <table class="table">
      <thead><tr><th>Nombre</th><th>Productos</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['nombre']) ?></td>
          <td><?= $it['total'] ?></td>
          <td>
            <a href="?delete=<?= $it['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que quieres eliminar esta categoría?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$items): ?>
        <tr><td colspan="3" class="empty">No hay categorías registradas.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
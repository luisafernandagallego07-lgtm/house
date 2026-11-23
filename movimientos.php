<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
log_modulo('movimientos');
$page = 'Movimientos';

$preseleccion = (int)($_GET['producto'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productoId = (int)($_POST['producto_id'] ?? 0);
    $tipo = $_POST['tipo'] === 'salida' ? 'salida' : 'entrada';
    $cantidad = max(1, (int)($_POST['cantidad'] ?? 0));
    $motivo = trim($_POST['motivo'] ?? '');
    $u = current_user();

    if ($productoId) {
        $pdo = db();
        $pdo->beginTransaction();
        try {
            $p = $pdo->prepare('SELECT stock_actual FROM productos WHERE id=? FOR UPDATE');
            $p->execute([$productoId]);
            $row = $p->fetch();
            if (!$row) throw new Exception('Producto no existe');
            if ($tipo === 'salida' && $row['stock_actual'] < $cantidad)
                throw new Exception('Stock insuficiente');

            $delta = $tipo === 'entrada' ? $cantidad : -$cantidad;
            $pdo->prepare('UPDATE productos SET stock_actual = stock_actual + ? WHERE id=?')
                ->execute([$delta, $productoId]);

            $pdo->prepare('INSERT INTO movimientos (producto_id, usuario_id, tipo, cantidad, motivo) VALUES (?,?,?,?,?)')
                ->execute([$productoId, $u['id'], $tipo, $cantidad, $motivo]);

            $pdo->commit();
            $_SESSION['flash'] = 'Movimiento registrado correctamente.';
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_error($e->getMessage());
            $_SESSION['flash_error'] = $e->getMessage();
        }
        header('Location: movimientos.php');
        exit;
    }
}

$productos = db()->query('SELECT id, codigo, nombre, stock_actual FROM productos WHERE activo=1 ORDER BY nombre')->fetchAll();
$movs = db()->query('SELECT m.*, p.nombre producto, u.nombre usuario FROM movimientos m
  JOIN productos p ON p.id=m.producto_id
  JOIN usuarios u ON u.id=m.usuario_id
  ORDER BY m.fecha DESC LIMIT 100')->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<h1>Movimientos de inventario</h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="banner banner-ok"><?= htmlspecialchars($_SESSION['flash']) ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="banner banner-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<form method="post" class="card">
  <h2>Registrar movimiento</h2>
  <div class="grid-2">
    <label>Producto
      <select name="producto_id" required>
        <option value="">— Selecciona —</option>
        <?php foreach ($productos as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $p['id']==$preseleccion?'selected':'' ?>>
            <?= htmlspecialchars($p['codigo'].' · '.$p['nombre']) ?> (stock: <?= $p['stock_actual'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Tipo
      <select name="tipo">
        <option value="entrada">Entrada</option>
        <option value="salida">Salida</option>
      </select>
    </label>
    <label>Cantidad<input type="number" min="1" name="cantidad" value="1" required></label>
    <label>Motivo<input name="motivo" placeholder="Compra, venta, ajuste…"></label>
  </div>
  <button class="btn btn-primary">Registrar</button>
</form>

<h2>Últimos movimientos</h2>
<table class="table">
  <thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Motivo</th><th>Usuario</th></tr></thead>
  <tbody>
  <?php foreach ($movs as $m): ?>
    <tr>
      <td><?= date('d/m/Y H:i', strtotime($m['fecha'])) ?></td>
      <td><?= htmlspecialchars($m['producto']) ?></td>
      <td><span class="badge <?= $m['tipo']==='entrada'?'badge-success':'badge-warn' ?>"><?= $m['tipo'] ?></span></td>
      <td><?= $m['cantidad'] ?></td>
      <td><?= htmlspecialchars($m['motivo']) ?></td>
      <td><?= htmlspecialchars($m['usuario']) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
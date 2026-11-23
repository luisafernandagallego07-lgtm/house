<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
log_modulo('reportes');
$page = 'Reportes';

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

$stmt = db()->prepare("
  SELECT p.codigo, p.nombre, c.nombre AS categoria,
         COALESCE(SUM(CASE WHEN m.tipo='entrada' THEN m.cantidad END),0) entradas,
         COALESCE(SUM(CASE WHEN m.tipo='salida'  THEN m.cantidad END),0) salidas,
         p.stock_actual, p.stock_minimo
  FROM productos p
  LEFT JOIN categorias c ON c.id = p.categoria_id
  LEFT JOIN movimientos m ON m.producto_id = p.id AND DATE(m.fecha) BETWEEN ? AND ?
  WHERE p.activo = 1
  GROUP BY p.id
  ORDER BY p.nombre
");
$stmt->execute([$desde, $hasta]);
$rows = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<h1>Reporte de inventario</h1>

<form method="get" class="toolbar">
  <label>Desde <input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>"></label>
  <label>Hasta <input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>"></label>
  <button class="btn btn-primary">Filtrar</button>
  <button type="button" class="btn btn-ghost" onclick="window.print()">Imprimir / PDF</button>
</form>

<table class="table">
  <thead>
    <tr><th>Código</th><th>Producto</th><th>Categoría</th><th>Entradas</th><th>Salidas</th><th>Stock</th><th>Estado</th></tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= htmlspecialchars($r['codigo']) ?></td>
      <td><?= htmlspecialchars($r['nombre']) ?></td>
      <td><?= htmlspecialchars($r['categoria'] ?? '—') ?></td>
      <td><?= $r['entradas'] ?></td>
      <td><?= $r['salidas'] ?></td>
      <td><?= $r['stock_actual'] ?></td>
      <td>
        <?php if ($r['stock_actual'] == 0): ?><span class="badge badge-danger">Agotado</span>
        <?php elseif ($r['stock_actual'] <= $r['stock_minimo']): ?><span class="badge badge-warn">Bajo</span>
        <?php else: ?><span class="badge badge-success">OK</span><?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
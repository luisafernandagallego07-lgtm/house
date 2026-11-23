<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
log_modulo('dashboard');
$page = 'Dashboard';

// KPIs
$totalProductos = (int)db()->query('SELECT COUNT(*) FROM productos WHERE activo=1')->fetchColumn();
$stockBajo = db()->query('SELECT id, codigo, nombre, stock_actual, stock_minimo FROM productos WHERE activo=1 AND stock_actual <= stock_minimo ORDER BY stock_actual ASC LIMIT 10')->fetchAll();
$movRecientes = db()->query('SELECT m.*, p.nombre AS producto, u.nombre AS usuario FROM movimientos m
  JOIN productos p ON p.id=m.producto_id
  JOIN usuarios u ON u.id=m.usuario_id
  ORDER BY m.fecha DESC LIMIT 8')->fetchAll();
$errores = (int)db()->query('SELECT COUNT(*) FROM log_errores')->fetchColumn();
$usosHoy = (int)db()->query('SELECT COUNT(*) FROM log_uso_modulos WHERE DATE(fecha)=CURDATE()')->fetchColumn();
$categoriasCount = (int)db()->query('SELECT COUNT(*) FROM categorias')->fetchColumn();

// Datos para gráficos (últimos 7 días: entradas vs salidas)
$rows = db()->query("SELECT DATE(fecha) d, tipo, SUM(cantidad) total
                     FROM movimientos
                     WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                     GROUP BY DATE(fecha), tipo")->fetchAll();
$labels = []; $entradas = []; $salidas = [];
for ($i=6;$i>=0;$i--) {
  $d = date('Y-m-d', strtotime("-$i day"));
  $labels[] = date('d/m', strtotime($d));
  $entradas[$d] = 0; $salidas[$d] = 0;
}
foreach ($rows as $r) {
  if ($r['tipo']==='entrada') $entradas[$r['d']] = (int)$r['total'];
  else $salidas[$r['d']] = (int)$r['total'];
}

include __DIR__ . '/includes/header.php';
?>
<h1>Panel principal</h1>

<section class="kpi-grid">
  <div class="kpi-card"><span class="kpi-label">Productos activos</span><span class="kpi-value"><?= $totalProductos ?></span></div>
  <div class="kpi-card"><span class="kpi-label">Alertas de stock</span><span class="kpi-value kpi-warn"><?= count($stockBajo) ?></span></div>
  <div class="kpi-card"><span class="kpi-label">Categorías</span><span class="kpi-value"><?= $categoriasCount ?></span></div>
  <div class="kpi-card"><span class="kpi-label">Usos hoy</span><span class="kpi-value"><?= $usosHoy ?></span></div>
  <div class="kpi-card"><span class="kpi-label">Errores registrados</span><span class="kpi-value"><?= $errores ?></span></div>
</section>

<div class="tabs" data-tabs>
  <button class="tab active" data-tab="grafico">Movimientos</button>
  <button class="tab" data-tab="stock">Stock bajo</button>
  <button class="tab" data-tab="recientes">Recientes</button>
</div>

<section class="tab-panel active" data-panel="grafico">
  <h2>Entradas y salidas (últimos 7 días)</h2>
  <div class="chart-bars">
    <?php foreach ($labels as $i => $lb):
      $e = $entradas[array_keys($entradas)[$i]] ?? 0;
      $s = $salidas[array_keys($salidas)[$i]] ?? 0;
      $max = max(1, max($entradas), max($salidas));
    ?>
    <div class="chart-col">
      <div class="bars">
        <div class="bar bar-in" style="height: <?= ($e/$max)*100 ?>%" title="Entradas: <?= $e ?>"></div>
        <div class="bar bar-out" style="height: <?= ($s/$max)*100 ?>%" title="Salidas: <?= $s ?>"></div>
      </div>
      <span class="chart-label"><?= $lb ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="legend"><span class="dot dot-in"></span> Entradas <span class="dot dot-out"></span> Salidas</div>
</section>

<section class="tab-panel" data-panel="stock">
  <h2>Productos con stock bajo</h2>
  <?php if (!$stockBajo): ?>
    <p class="empty">Sin alertas. Todo en orden 🎉</p>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>Código</th><th>Producto</th><th>Stock</th><th>Mínimo</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($stockBajo as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['codigo']) ?></td>
          <td><?= htmlspecialchars($p['nombre']) ?></td>
          <td><span class="badge badge-danger"><?= $p['stock_actual'] ?></span></td>
          <td><?= $p['stock_minimo'] ?></td>
          <td><a href="movimientos.php?producto=<?= $p['id'] ?>" class="btn btn-sm">Reponer</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<section class="tab-panel" data-panel="recientes">
  <h2>Movimientos recientes</h2>
  <table class="table">
    <thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Usuario</th></tr></thead>
    <tbody>
    <?php foreach ($movRecientes as $m): ?>
      <tr>
        <td><?= date('d/m/Y H:i', strtotime($m['fecha'])) ?></td>
        <td><?= htmlspecialchars($m['producto']) ?></td>
        <td><span class="badge <?= $m['tipo']==='entrada'?'badge-success':'badge-warn' ?>"><?= $m['tipo'] ?></span></td>
        <td><?= $m['cantidad'] ?></td>
        <td><?= htmlspecialchars($m['usuario']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$movRecientes): ?>
      <tr><td colspan="5" class="empty">Sin movimientos</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
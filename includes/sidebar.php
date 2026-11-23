<?php $page = $page ?? ''; ?>
<?php $u = current_user(); ?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">STOCKLY</div>
  <nav class="sidebar-nav">
    <a href="index.php"       class="<?= $page==='Dashboard'?'active':'' ?>">📊 Dashboard</a>
    <a href="productos.php"   class="<?= $page==='Productos'?'active':'' ?>">📦 Productos</a>
    <a href="categorias.php"  class="<?= $page==='Categorías'?'active':'' ?>">🏷️ Categorías</a>
    <a href="proveedores.php" class="<?= $page==='Proveedores'?'active':'' ?>">🚚 Proveedores</a>
    <a href="movimientos.php" class="<?= $page==='Movimientos'?'active':'' ?>">🔁 Movimientos</a>
    <a href="reportes.php"    class="<?= $page==='Reportes'?'active':'' ?>">📈 Reportes</a>
    <?php if ($u['rol'] === 'admin'): ?>
      <a href="usuarios.php"  class="<?= $page==='Usuarios'?'active':'' ?>">👤 Usuarios</a>
    <?php endif; ?>
  </nav>
</aside>
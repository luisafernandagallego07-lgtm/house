<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
log_modulo('productos');
$page = 'Productos';

$cats = db()->query('SELECT id,nombre FROM categorias ORDER BY nombre')->fetchAll();
$provs = db()->query('SELECT id,nombre FROM proveedores ORDER BY nombre')->fetchAll();

// Eliminar producto (baja lógica)
if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    try {
        db()->prepare('UPDATE productos SET activo=0 WHERE id=?')->execute([(int)$_GET['delete']]);
        $_SESSION['flash'] = 'Producto eliminado.';
    } catch (Throwable $e) {
        log_error($e->getMessage());
        $_SESSION['flash_error'] = 'No se pudo eliminar el producto.';
    }
    header('Location: productos.php');
    exit;
}

// Guardar (crear / editar) producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $codigo = trim($_POST['codigo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoriaId = ($_POST['categoria_id'] ?? '') !== '' ? (int)$_POST['categoria_id'] : null;
    $proveedorId = ($_POST['proveedor_id'] ?? '') !== '' ? (int)$_POST['proveedor_id'] : null;
    $precioCompra = (float)($_POST['precio_compra'] ?? 0);
    $precioVenta = (float)($_POST['precio_venta'] ?? 0);
    $stockActual = (int)($_POST['stock_actual'] ?? 0);
    $stockMinimo = (int)($_POST['stock_minimo'] ?? 5);

    if ($codigo === '' || $nombre === '') {
        $_SESSION['flash_error'] = 'Código y nombre son obligatorios.';
        header('Location: productos.php');
        exit;
    }

    try {
        if ($id) {
            db()->prepare('UPDATE productos SET codigo=?,nombre=?,descripcion=?,categoria_id=?,proveedor_id=?,precio_compra=?,precio_venta=?,stock_actual=?,stock_minimo=? WHERE id=?')
                ->execute([$codigo,$nombre,$descripcion,$categoriaId,$proveedorId,$precioCompra,$precioVenta,$stockActual,$stockMinimo,$id]);
            $_SESSION['flash'] = 'Producto actualizado.';
        } else {
            db()->prepare('INSERT INTO productos (codigo,nombre,descripcion,categoria_id,proveedor_id,precio_compra,precio_venta,stock_actual,stock_minimo) VALUES (?,?,?,?,?,?,?,?,?)')
                ->execute([$codigo,$nombre,$descripcion,$categoriaId,$proveedorId,$precioCompra,$precioVenta,$stockActual,$stockMinimo]);
            $_SESSION['flash'] = 'Producto creado.';
        }
    } catch (Throwable $e) {
        log_error($e->getMessage());
        $msg = $e->getMessage();
        if (stripos($msg, 'Duplicate entry') !== false) {
            $msg = 'Ya existe un producto con ese código.';
        } elseif (stripos($msg, 'foreign key constraint') !== false) {
            $msg = 'La categoría o el proveedor seleccionado no es válido.';
        } else {
            $msg = 'No se pudo guardar el producto.';
        }
        $_SESSION['flash_error'] = $msg;
    }
    header('Location: productos.php');
    exit;
}

include __DIR__ . '/includes/header.php';
?>
<h1>Productos</h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="banner banner-ok"><?= htmlspecialchars($_SESSION['flash']) ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="banner banner-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="toolbar">
  <input id="searchProducto" class="input" placeholder="Buscar por código o nombre…" style="max-width:320px">
  <select id="filterCategoria" class="input" style="max-width:200px">
    <option value="">Todas las categorías</option>
    <?php foreach ($cats as $c): ?>
      <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn-primary" id="btnNuevoProducto" type="button">+ Nuevo producto</button>
</div>

<table class="table" id="tablaProductos">
  <thead>
    <tr>
      <th data-sort="codigo">Código</th>
      <th data-sort="nombre">Nombre</th>
      <th data-sort="categoria">Categoría</th>
      <th data-sort="precio_venta">Precio</th>
      <th data-sort="stock_actual">Stock</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody><tr><td colspan="6" class="empty">Cargando…</td></tr></tbody>
</table>
<div class="pagination" id="paginacionProductos"></div>

<!-- Modal producto -->
<div class="modal" id="modalProducto" role="dialog" aria-modal="true">
  <div class="modal-content">
    <h2 id="modalProductoTitulo">Nuevo producto</h2>
    <form id="formProducto" method="post" action="productos.php">
      <input type="hidden" name="id">
      <label>Código *<input name="codigo" required></label>
      <label>Nombre *<input name="nombre" required></label>
      <label>Descripción<textarea name="descripcion"></textarea></label>
      <label>Categoría
        <select name="categoria_id">
          <option value="">— Sin categoría —</option>
          <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label>Proveedor
        <select name="proveedor_id">
          <option value="">— Sin proveedor —</option>
          <?php foreach ($provs as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option><?php endforeach; ?>
        </select>
      </label>
      <div class="grid-2">
        <label>Precio compra<input type="number" step="0.01" name="precio_compra" value="0"></label>
        <label>Precio venta<input type="number" step="0.01" name="precio_venta" value="0"></label>
        <label>Stock inicial<input type="number" name="stock_actual" value="0"></label>
        <label>Stock mínimo<input type="number" name="stock_minimo" value="5"></label>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" data-close-modal>Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal confirmación borrado -->
<div class="modal" id="modalConfirm" role="dialog" aria-modal="true">
  <div class="modal-content small">
    <h2>¿Eliminar producto?</h2>
    <p>Esta acción no se puede deshacer.</p>
    <div class="modal-actions">
      <button class="btn btn-ghost" data-close-modal>Cancelar</button>
      <button class="btn btn-danger" id="btnConfirmDelete">Eliminar</button>
    </div>
  </div>
</div>

<script>
window.CATEGORIAS = <?= json_encode($cats) ?>;
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>

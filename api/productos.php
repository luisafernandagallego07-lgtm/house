<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$in = json_decode(file_get_contents('php://input'), true) ?: $_POST;

try {
    if ($method === 'OPTIONS') {
        http_response_code(204);
        header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        exit;
    }
    switch ($method) {
        case 'GET':
            if ($id) {
                $s = db()->prepare('SELECT * FROM productos WHERE id=?');
                $s->execute([$id]);
                $p = $s->fetch();
                if (!$p) { http_response_code(404); echo json_encode(['error'=>'No encontrado']); exit; }
                echo json_encode($p); exit;
            }
            
            $q = trim($_GET['q'] ?? '');
            $cat = (int)($_GET['categoria'] ?? 0);
            $sql = 'SELECT p.*, c.nombre categoria FROM productos p LEFT JOIN categorias c ON c.id=p.categoria_id WHERE p.activo=1';
            $params = [];
            if ($q !== '') { $sql .= ' AND (p.codigo LIKE ? OR p.nombre LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
            if ($cat)      { $sql .= ' AND p.categoria_id = ?'; $params[] = $cat; }
            $sql .= ' ORDER BY p.nombre';
            $s = db()->prepare($sql);
            $s->execute($params);
            $productos = $s->fetchAll();

            // Buscar cuántos productos tienen stock bajo
            $bajos = array_filter($productos, fn($p) => $p['stock_actual'] <= $p['stock_minimo']);
            
            // Devolvemos los productos y el aviso si es que hay alguno bajo
            echo json_encode([
                'productos' => array_values($productos),
                'alerta_stock' => count($bajos) > 0 ? count($bajos) : 0
            ]); 
            exit;

        case 'POST':
            $fields = ['codigo','nombre','descripcion','categoria_id','proveedor_id','precio_compra','precio_venta','stock_actual','stock_minimo'];
            $vals = array_map(fn($f) => $in[$f] ?? null, $fields);
            $vals[0] = trim((string)($vals[0] ?? ''));
            $vals[1] = trim((string)($vals[1] ?? ''));
            if ($vals[0] === '' || $vals[1] === '') { http_response_code(400); echo json_encode(['error'=>'Código y nombre obligatorios']); exit; }
            
            // Limpiamos los IDs vacíos para que no rompan la llave foránea
            $vals[3] = !empty($vals[3]) ? (int)$vals[3] : null; 
            $vals[4] = !empty($vals[4]) ? (int)$vals[4] : null;

            $s = db()->prepare('INSERT INTO productos (codigo,nombre,descripcion,categoria_id,proveedor_id,precio_compra,precio_venta,stock_actual,stock_minimo) VALUES (?,?,?,?,?,?,?,?,?)');
            $s->execute($vals);
            echo json_encode(['id' => db()->lastInsertId()]); exit;

        case 'PUT':
            if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID requerido']); exit; }
            
            // Limpiamos los IDs vacíos aquí también
            $cat_id = !empty($in['categoria_id']) ? (int)$in['categoria_id'] : null;
            $prov_id = !empty($in['proveedor_id']) ? (int)$in['proveedor_id'] : null;

            $s = db()->prepare('UPDATE productos SET codigo=?,nombre=?,descripcion=?,categoria_id=?,proveedor_id=?,precio_compra=?,precio_venta=?,stock_actual=?,stock_minimo=? WHERE id=?');
            $s->execute([
                $in['codigo'] ?? '', 
                $in['nombre'] ?? '', 
                $in['descripcion'] ?? null,
                $cat_id, 
                $prov_id,
                $in['precio_compra'] ?? 0, 
                $in['precio_venta'] ?? 0,
                $in['stock_actual'] ?? 0, 
                $in['stock_minimo'] ?? 5, 
                $id
            ]);
            echo json_encode(['ok' => true]); exit;

        case 'DELETE':
            if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID requerido']); exit; }
            db()->prepare('UPDATE productos SET activo=0 WHERE id=?')->execute([$id]);
            echo json_encode(['ok' => true]); exit;
    }
} catch (Throwable $e) {
    log_error($e->getMessage());
    $msg = $e->getMessage();
    if (stripos($msg, 'Duplicate entry') !== false) {
        $msg = 'Ya existe un producto con ese código.';
    } elseif (stripos($msg, 'foreign key constraint') !== false) {
        $msg = 'La categoría o el proveedor seleccionado no es válido.';
    }
    http_response_code(500);
    echo json_encode(['error' => $msg]);
}
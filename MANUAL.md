# Manual de STOCKLY

Sistema web de gestión de inventario para PyMEs. PHP 8.1+ + MySQL/MariaDB + HTML/CSS/JS nativo (sin frameworks).

---

## 1. Descripción general

STOCKLY permite administrar productos, categorías, proveedores, movimientos de entrada/salida de mercancía, reportes de inventario y usuarios con roles (admin/editor). Incluye alertas de stock bajo, gráficos de movimientos en el dashboard y registro de uso/errores.

---

## 2. Requisitos e instalación

- PHP 8.1+
- MySQL 8 / MariaDB 10.4+
- Apache/Nginx o `php -S localhost:8000`

Pasos:
1. Copiar el proyecto en el servidor web (ej. `C:\xampp\htdocs\STOCKLY`).
2. Crear la base de datos e importar el esquema:
   ```bash
   mysql -u root -p stockly < sql/stockly.sql
   ```
   > Nota: el archivo `sql/stockly.sql` está vacío en este repo; el esquema real vive en la BD `stockly` del servidor. Exportarlo con `mysqldump -u root stockly > sql/stockly.sql`.
3. Configurar credenciales en `config/database.php` (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
4. Abrir `http://localhost/STOCKLY/login.php`. Usuario demo: `admin@stockly.local` / `Admin123!`.

---

## 3. Estructura de carpetas

```
STOCKLY/
├── index.php              # Dashboard
├── login.php / logout.php # Autenticación
├── productos.php          # UI productos (modal + tabla JS)
├── categorias.php         # CRUD categorías (server-side)
├── proveedores.php        # CRUD proveedores (server-side)
├── movimientos.php        # Registro de entradas/salidas
├── reportes.php           # Reporte filtrable + imprimir
├── usuarios.php           # Gestión de usuarios (solo admin)
├── api/
│   └── productos.php      # REST JSON para productos (GET/POST/PUT/DELETE)
├── config/
│   └── database.php       # Conexión PDO (singleton db())
├── includes/
│   ├── auth.php           # Sesiones, roles, logs
│   ├── header.php         # Layout superior (requiere login)
│   ├── sidebar.php        # Menú lateral
│   └── footer.php         # Cierra layout + carga app.js
├── assets/
│   ├── css/style.css
│   └── js/app.js          # Toda la lógica frontend
└── sql/stockly.sql        # Esquema (vacío; generar con mysqldump)
```

---

## 4. Arquitectura y flujo de una petición

- Cada página `.php` sigue el mismo patrón:
  1. `require_once config/database.php` y `includes/auth.php`.
  2. `require_login()` (y `require_admin()` si aplica).
  3. `log_modulo('nombre')` para métricas de uso.
  4. Consultas a BD con `db()` (PDO).
  5. `include includes/header.php` (abre HTML + sidebar).
  6. HTML de la vista.
  7. `include includes/footer.php` (cierra + carga `app.js`).
- La BD se accede mediante `db()` en `config/database.php`: singleton PDO con `ERRMODE_EXCEPTION`, fetch asociativo, prepared statements (sin emulación).
- La autenticación vive en `includes/auth.php`: sesión de 8h, cierre por inactividad de 15 min (deslizante), regeneración de ID cada 30 min.

---

## 5. Base de datos (tablas usadas por la app)

| Tabla | Uso |
|---|---|
| `usuarios` | Cuentas: nombre, email, password_hash, rol (admin/editor), activo, intentos fallidos, bloqueo |
| `categorias` | Categorías de productos (nombre único) |
| `proveedores` | Proveedores |
| `productos` | Productos: código único, nombre, descripción, categoria_id, proveedor_id, precios, stock_actual, stock_minimo, activo |
| `movimientos` | Entradas/salidas: producto_id, usuario_id, tipo, cantidad, motivo, fecha |
| `log_errores` | Errores capturados |
| `log_uso_modulos` | Uso por módulo (métricas dashboard) |

Relaciones: `productos.categoria_id → categorias.id` (SET NULL), `productos.proveedor_id → proveedores.id` (SET NULL), `movimientos.producto_id → productos.id` (CASCADE), `movimientos.usuario_id → usuarios.id`.

---

## 6. Módulos funcionales

- **Dashboard (`index.php`)**: KPIs (productos activos, alertas stock, categorías, usos hoy, errores), gráfico de barras de entradas/salidas últimos 7 días, tabla de stock bajo y movimientos recientes. Tabs controladas por `app.js` (`[data-tabs]`).
- **Productos (`productos.php` + `api/productos.php`)**: tabla con búsqueda, filtro por categoría, orden, paginación (JS). CRUD vía fetch JSON. Eliminación lógica (`activo=0`). Alerta toast si hay stock bajo.
- **Categorías (`categorias.php`)**: CRUD server-side con form POST. No permite borrar si tiene productos (FK). Mensajes flash en sesión.
- **Proveedores (`proveedores.php`)**: CRUD server-side.
- **Movimientos (`movimientos.php`)**: registra entrada/salida en transacción (`FOR UPDATE`), valida stock suficiente, actualiza `stock_actual`, inserta movimiento, alerta si queda por debajo del mínimo.
- **Reportes (`reportes.php`)**: filtro por rango de fechas, agrega entradas/salidas por producto, estado (Agotado/Bajo/OK). Imprimir con `window.print()`.
- **Usuarios (`usuarios.php`)**: solo admin. Crea usuarios con contraseña fuerte (8+ chars, mayús, minús, número). Activar/desactivar.

---

## 7. Autenticación y roles

- `require_login()`: redirige a `login.php` si no hay sesión; cierra por inactividad; renueva ID.
- `require_admin()`: además exige `rol === 'admin'` (403 si no).
- Login: bloqueo tras 5 intentos fallidos por 15 min (`bloqueado_hasta`). Contraseña verificada con `password_verify`. Resetea intentos al entrar.
- Roles: `admin` (todo + usuarios), `editor` (resto). Sidebar muestra "Usuarios" solo a admin.

---

## 8. API REST (`api/productos.php`)

Base: `api/productos.php`. Requiere sesión (cookie). JSON.

- `GET` (sin id): lista activos. Filtros `?q=` y `?categoria=`. Respuesta `{ productos, alerta_stock }`.
- `GET ?id=`: un producto.
- `POST`: crea. Body JSON con campos: codigo, nombre, descripcion, categoria_id, proveedor_id, precio_compra, precio_venta, stock_actual, stock_minimo.
- `PUT ?id=`: actualiza.
- `DELETE ?id=`: baja lógica (`activo=0`).

Errores devueltos como `{ error }` con código HTTP. Los errores se registran en `log_errores` vía `log_error()`.

---

## 9. Frontend

- `assets/js/app.js`: helpers `$`, `$$`, `toast`, `openModal`/`closeModals`, `debounce`.
- Sidebar colapsable (`#toggleSidebar`).
- Tabs del dashboard: `[data-tabs] .tab` alterna `.tab-panel`.
- Modales: clase `.open`; cierre por click en overlay o `[data-close-modal]`.
- Productos: estado en objeto `state` (page, perPage, q, categoria, sort, dir, data); fetch a la API; render con paginación.
- CSS en `assets/css/style.css` (no framework).
- Nota: `login.php` referencia `assets/css/styles.css` (plural) mientras el resto usa `assets/css/style.css`; verificar si existe.

---

## 10. Cómo agregar cosas nuevas

### 10.1 Nueva página/módulo
1. Crear `nuevo.php` en la raíz siguiendo el patrón:
   ```php
   <?php
   require_once __DIR__ . '/config/database.php';
   require_once __DIR__ . '/includes/auth.php';
   require_login();            // o require_admin()
   log_modulo('nuevo');
   $page = 'Nuevo';
   // consultas...
   include __DIR__ . '/includes/header.php';
   ?>
   <!-- HTML -->
   <?php include __DIR__ . '/includes/footer.php'; ?>
   ```
2. Agregar entrada en `includes/sidebar.php` (y condicionar por rol si es admin).
3. Si necesita CRUD server-side, manejar POST/GET en el mismo archivo (como `categorias.php`).

### 10.2 Nuevo endpoint API
- Crear `api/nuevo.php`, copiar estructura de `api/productos.php`: require database/auth, `require_login()`, `header('Content-Type: application/json')`, switch por `$method`, try/catch con `log_error`. Consumir desde `app.js` con `fetch('api/nuevo.php')`.

### 10.3 Nueva tabla/campo
1. Crear tabla en MySQL (y documentar en `sql/stockly.sql` exportando).
2. Si es FK a productos/categorías, agregar constraints.
3. Actualizar consultas en las páginas afectadas.
4. Para soft-delete, usar columna `activo`.

### 10.4 Nuevo KPI en dashboard
- Agregar consulta en `index.php` (sección KPIs) y un `<div class="kpi-card">` en la grid. Para gráficos, agregar datos al bloque de `$rows` y renderizar en `chart-bars`.

### 10.5 Nuevo log de módulo
- Llamar `log_modulo('nombre_modulo')` al inicio de la página; queda registrado en `log_uso_modulos` y se cuenta en "Usos hoy".

---

## 11. Logs y métricas

- Uso por módulo: tabla `log_uso_modulos` (usuario_id, modulo, fecha).
- Errores: `log_errores` (mensaje, usuario_id, fecha), alimentada por `log_error()`.
- Dashboard muestra conteos totales.

---

## 12. Configuración

- `config/database.php`: credenciales y PDO.
- `includes/auth.php`: `SESSION_IDLE_LIMIT` (15 min), duración de cookie (8h), regeneración (30 min), intentos de bloqueo (en `login.php`, 5 intentos → 15 min).

---

## 13. Solución de problemas

- **Error de conexión a BD**: revisar `config/database.php` y que el servicio MySQL esté activo.
- **Sesión expirada**: cierre por inactividad 15 min; reingresar.
- **Cuenta bloqueada**: esperar 15 min o resetear `intentos_fallidos`/`bloqueado_hasta` en BD.
- **No se puede borrar categoría**: tiene productos asociados; reasignar/eliminar productos primero.
- **Stock insuficiente al registrar salida**: verificar `stock_actual`.
- **`sql/stockly.sql` vacío**: generar con `mysqldump -u root stockly > sql/stockly.sql`.

---

## 14. Notas de mantenimiento

- Hay un duplicado: `api/movimientos.php` contiene código de página (no REST) igual a `movimientos.php`. Si se busca API de movimientos, no existe; considerar refactorizar.
- `api/categorias.php`, `api/proveedores.php`, `api/dashboard.php` están vacíos: los CRUD de categorías/proveedores son server-side en sus páginas.
- CSS: confirmar nombre de archivo (`style.css` vs `styles.css`).

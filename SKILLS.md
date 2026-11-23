# Especificación técnica: STOCKLY

> Desarrollar un sistema web llamado STOCKLY que permita administrar y controlar el inventario de pequeñas y medianas tiendas de manera organizada y eficiente. El sistema facilitará el registro de productos, control de entradas y salidas, seguimiento de existencias y generación de reportes para mejorar la gestión del negocio.

**Alcance**

Incluye:
- Registro, edición y eliminación de productos.
- Clasificación por categorías.
- Registro de proveedores.
- Control de entradas y salidas de mercancía.
- Consulta de existencias disponibles.
- Alertas de productos con poco stock.
- Registro e historial de movimientos.
- Panel principal con estadísticas.
- Generación de reportes de inventario.

No incluye inicialmente:
- Contabilidad avanzada.
- Nómina de empleados.
- Facturación electrónica.
- Integración con bancos.
- Comercio electrónico.
- Aplicación móvil independiente.

**Contexto general**

STOCKLY es un software de gestión de inventarios diseñado para facilitar la administración de pequeñas y medianas tiendas. Surge de la necesidad de reemplazar los controles manuales de mercancía, reducir errores en el registro de productos y evitar pérdidas por falta de organización.

El sistema contará con una interfaz moderna, intuitiva y fácil de utilizar. Permitirá a los administradores mantener actualizada la información de sus productos, conocer las cantidades disponibles, identificar mercancía próxima a agotarse y consultar reportes que faciliten la toma de decisiones.

Se desarrollará como una aplicación web utilizando HTML, CSS y JavaScript para el frontend, PHP para el backend y MySQL para la gestión de la base de datos.

_Generado con SkillNeitor 1.2.0 · 2026-11-12 · 0 ítems · 0 categorías_

# Definición de producto

_Decisiones registradas por el wizard de SkillNeitor. Los pasos no aplicables se muestran explícitamente._

## Fase A — Producto y experiencia

### Paso 1 — Interfaz de usuario y categorías

**Skill:** UI Type Selector
**Estado:** Completo

**Tipo de interfaz:** Web app
**Categorías del producto:** Autenticación, Gestión de usuarios, Reportes, Catálogos, Dashboards

### Paso 2 — Experiencia de usuario y comportamiento

**Skill:** UX Behavior Pattern
**Estado:** Completo

**Flujo:** Libre
**Densidad:** Cómoda
**Feedback:** Toasts, Inline
**Navegación:** Tabs
**Accesibilidad:** WCAG AAA

### Paso 3 — Componentes estándar de UI

**Skill:** UI Component Catalog
**Estado:** Completo

**Panel de control:** Página dedicada
**Dropdown de opciones:** En header
**Sidebar: disposición:** Colapsable
**Sidebar: comportamiento:** Pin, Hover-expand
**Menú hamburguesa:** Siempre visible

### Paso 4 — Elementos de UI + comportamiento UX

**Skill:** Element-Behavior Mapping
**Estado:** Completo

**Formularios:** Validación inline
**Tablas:** Paginación, Orden, Filtros
**Notificaciones:** Toast, Banner
**Estados vacíos:** Simple
**Loaders / skeletons:** Spinner
**Confirmaciones destructivas:** Modal de confirmación

## Fase B — Stack técnico y datos

### Paso 5 — Tecnologías, librerías y usos

**Skill:** Tech Stack Declaration
**Estado:** Completo

**Frontend:** HTML5 + CSS3 + JavaScript
**Backend:** PHP
**Librerías de UI:** NINGUNO
**Gestión de estado:** JavaScript nativo
**Testing:** PHPUnit
**Uso declarado de cada pieza:** HTML: Estructura de las páginas del sistema.

CSS: Diseño y apariencia visual de STOCKLY.

JavaScript: Interacciones y validaciones básicas.

PHP: Procesamiento de información y conexión con la base de datos.

Librerías de UI: No se utilizarán.

Gestión de estado: Control de las interacciones del sistema.

Testing: Pruebas manuales para verificar el funcionamiento.

Build: No se utilizarán herramientas de compilación.

### Paso 6 — Estilo de backend y principios

**Skill:** Backend Style & Principles
**Estado:** Completo

**Estilo:** REST
**Principios:** DRY, KISS

### Paso 7 — Tipos de base de datos

**Skill:** Data Store Selector
**Estado:** Completo

**Tipos:** Relacional (SQL)
**Justificación:** Se utilizará una base de datos relacional (SQL) porque permite almacenar y organizar la información de STOCKLY mediante tablas relacionadas. Facilita la administración de productos, categorías, proveedores, usuarios y movimientos de inventario, garantizando un manejo ordenado de los datos y evitando información duplicada. Se utilizará MySQL por ser una herramienta sencilla y compatible con PHP.

### Paso 8 — Procesamiento y analítica de datos

**Skill:** Data & Analytics Pipeline
**Estado:** Completo

**Ingesta:** Manual
**Procesamiento:** Sin procesamiento
**Transformación:** Limpieza, Normalización
**Almacenamiento analítico:** Tablas agregadas

## Fase C — Inteligencia artificial y analítica

### Paso 9 — Agentes de IA (local / online)

**Skill:** AI Agent Selector
**Estado:** Completo

**Modo:** Sin IA
**Runtime local:** undefined
**Proveedor online:** undefined
**Manejo de API key:** undefined

### Paso 10 — Asistencia por IA y chatbot

**Skill:** AI Assistant & Chatbot
**Estado:** No aplica — La respuesta del paso anterior desactiva este paso.

_La respuesta del paso anterior desactiva este paso._

### Paso 11 — Métricas, KPI, EDA y visualización

**Skill:** Analytics & Visualization Catalog
**Estado:** Completo

**Métricas y KPI:** Tiempo activo, Errores, Uso por módulo, Métrica personalizada
**Fórmula y unidad de cada métrica:** 1. Cantidad de productos registrados: contar todos los productos activos. Unidad: productos.

2. Uso por módulo: número de veces que se utiliza cada módulo. Unidad: usos.

3. Errores: contar los errores registrados en el sistema. Unidad: errores.

4. Tiempo activo: tiempo total de uso del sistema. Unidad: minutos.
**Estadísticas descriptivas:** Media, Moda
**EDA:** Valores nulos, Tendencias
**Tipos de gráficos:** Barras, Líneas, Pastel
**Frecuencia de actualización:** Diaria

## Fase D — Operación, seguridad y distribución

### Paso 12 — Seguridad de login/logout

**Skill:** Auth & Session Security
**Estado:** Completo

**Método de login:** Usuario/contraseña
**Duración de sesión:** 8 horas
**Renovación:** Deslizante
**Cierre por inactividad:** 15 minutos
**Logout global:** No
**2FA:** Sin 2FA
**Bloqueo por intentos:** 5 intentos
**Política de contraseñas:** Fuerte
**Recuperación:** Email

### Paso 13 — Mono usuario vs. multiusuario

**Skill:** User Model Selector
**Estado:** Completo

**Modelo:** Multiusuario con roles
**Roles:** Admin, Editor
**Jerarquía:** Plana
**Aislamiento de datos:** Sin aislamiento

### Paso 14 — Persistencia local (localStorage / PWA)

**Skill:** Local Persistence Selector
**Estado:** Completo

**Modo de persistencia:** Sin persistencia local
**¿PWA?:** No
**Sincronización con backend:** Automática
**Estrategia de conflicto:** Last write wins

### Paso 15 — Compilación y plataforma destino

**Skill:** Build & Deploy Target
**Estado:** Completo

**Compilación:** Build web estático
**Plataformas:** Web
**Empaquetadores:** Otro

### Paso 16 — Repositorio y acciones asociadas

**Skill:** Repository & VCS Actions
**Estado:** Completo

**¿Maneja repositorio?:** Sí
**Proveedor:** GitHub
**Visibilidad:** Privado
**Acciones:** Init, Branch, Commit, Push, Pull
**Estrategia de ramas:** undefined
**Notas de repositorio:** Se utilizará GitHub con un repositorio privado para almacenar y organizar el código fuente de STOCKLY. Se realizarán commits para guardar los cambios y operaciones de push y pull para sincronizar el proyecto. Se manejará una estrategia de ramas Trunk-based, manteniendo una estructura sencilla y fácil de administrar.

## Resumen de decisiones

| Fase | Paso | Estado | Resumen |
| --- | --- | --- | --- |
| A | 1. Interfaz de usuario y categorías | Completo | Web app; Autenticación, Gestión de usuarios, Reportes, Catálogos, Dashboards |
| A | 2. Experiencia de usuario y comportamiento | Completo | Libre; Cómoda; Toasts, Inline; Tabs; WCAG AAA |
| A | 3. Componentes estándar de UI | Completo | Página dedicada; En header; Colapsable; Pin, Hover-expand; Siempre visible |
| A | 4. Elementos de UI + comportamiento UX | Completo | Validación inline; Paginación, Orden, Filtros; Toast, Banner; Simple; Spinner; Modal de confirmación |
| B | 5. Tecnologías, librerías y usos | Completo | HTML5 + CSS3 + JavaScript; PHP; NINGUNO; JavaScript nativo; PHPUnit; HTML: Estructura de las páginas del sistema.

CSS: Diseño y apariencia visual de STOCKLY.

JavaScript: Interacciones y validaciones básicas.

PHP: Procesamiento de información y conexión con la base de datos.

Librerías de UI: No se utilizarán.

Gestión de estado: Control de las interacciones del sistema.

Testing: Pruebas manuales para verificar el funcionamiento.

Build: No se utilizarán herramientas de compilación. |
| B | 6. Estilo de backend y principios | Completo | REST; DRY, KISS |
| B | 7. Tipos de base de datos | Completo | Relacional (SQL); Se utilizará una base de datos relacional (SQL) porque permite almacenar y organizar la información de STOCKLY mediante tablas relacionadas. Facilita la administración de productos, categorías, proveedores, usuarios y movimientos de inventario, garantizando un manejo ordenado de los datos y evitando información duplicada. Se utilizará MySQL por ser una herramienta sencilla y compatible con PHP. |
| B | 8. Procesamiento y analítica de datos | Completo | Manual; Sin procesamiento; Limpieza, Normalización; Tablas agregadas |
| C | 9. Agentes de IA (local / online) | Completo | Sin IA; undefined; undefined; undefined |
| C | 10. Asistencia por IA y chatbot | No aplica — La respuesta del paso anterior desactiva este paso. | La respuesta del paso anterior desactiva este paso. |
| C | 11. Métricas, KPI, EDA y visualización | Completo | Tiempo activo, Errores, Uso por módulo, Métrica personalizada; 1. Cantidad de productos registrados: contar todos los productos activos. Unidad: productos.

2. Uso por módulo: número de veces que se utiliza cada módulo. Unidad: usos.

3. Errores: contar los errores registrados en el sistema. Unidad: errores.

4. Tiempo activo: tiempo total de uso del sistema. Unidad: minutos.; Media, Moda; Valores nulos, Tendencias; Barras, Líneas, Pastel; Diaria |
| D | 12. Seguridad de login/logout | Completo | Usuario/contraseña; 8 horas; Deslizante; 15 minutos; No; Sin 2FA; 5 intentos; Fuerte; Email |
| D | 13. Mono usuario vs. multiusuario | Completo | Multiusuario con roles; Admin, Editor; Plana; Sin aislamiento |
| D | 14. Persistencia local (localStorage / PWA) | Completo | Sin persistencia local; No; Automática; Last write wins |
| D | 15. Compilación y plataforma destino | Completo | Build web estático; Web; Otro |
| D | 16. Repositorio y acciones asociadas | Completo | Sí; GitHub; Privado; Init, Branch, Commit, Push, Pull; undefined; Se utilizará GitHub con un repositorio privado para almacenar y organizar el código fuente de STOCKLY. Se realizarán commits para guardar los cambios y operaciones de push y pull para sincronizar el proyecto. Se manejará una estrategia de ramas Trunk-based, manteniendo una estructura sencilla y fácil de administrar. |

---

---

<!-- Avisos:
- No hay ítems seleccionados.
-->

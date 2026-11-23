# STOCKLY

Sistema web de gestión de inventario para PyMEs. PHP + MySQL + HTML/CSS/JS (sin frameworks).

## Requisitos
- PHP 8.1+
- MySQL 8 / MariaDB 10.4+
- Apache o Nginx (o `php -S` para desarrollo)

## Instalación

1. Clona o copia el proyecto en tu servidor web.
2. Crea la base de datos:
   ```bash
   mysql -u root -p < sql/stockly.sql
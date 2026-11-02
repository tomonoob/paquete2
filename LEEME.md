# Paquete 2

El menú principal es `index.php`. Cada apartado está en su carpeta:

```
index.php            Menú principal
clientes/            Registrar, consultar, mostrar y actualizar clientes
productos_factura/   Facturas, productos y pagos
tablas/              Página estilo periódico (HTML y CSS)
banco/               Login, registro, saldo, consignar y retirar
  inc/               conexion.php, funciones.php, esquema.php, cabecera.php, pie.php
  assets/            css, js e imágenes
comun/               conexion.php (Aiven), css y fuentes que usan varios apartados
sql/                 Scripts SQL
```

## Banco
- `index.php`: formulario de ingreso y revisión del usuario.
- `registro.php`: crea un usuario nuevo.
- `cuenta.php`: saldo e historial de movimientos.
- `movimiento.php`: consignar y retirar (según el parámetro `tipo`).
- `estado.php`: muestra si la base de datos está conectada y crea las tablas con un botón.
- Cuenta de prueba (funciona sin base de datos): usuario `demo`, contraseña `1234`.
- Todas las consultas se hacen con procedimientos almacenados (`CALL sp_cuenta_...`), que están en `inc/esquema.php` y en `sql/banco.sql`.

## Base de datos
La contraseña va en la variable de entorno `DB_PASSWORD`. Si no existe, `comun/conexion.php` la busca en el archivo `.env` de la raíz.

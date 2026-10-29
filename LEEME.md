

```
index.php            Menú principal (elige el apartado)
clientes/            Registrar, consultar, mostrar y actualizar clientes
productos_factura/   Facturas, productos y pagos
tablas/              Página estilo periódico (solo HTML y CSS)
banco/               Login, registro, saldo y abonos
  inc/               config, conexión, esquema, cabecera y pie
  assets/            css, js e imágenes
  estado.php         Estado de la base de datos y botón "Preparar base de datos"
comun/               Lo que comparten los apartados
  conexion.php       Conexión a Aiven (usa DB_PASSWORD)
  css/, fonts/       Estilos y fuentes
sql/                 Scripts SQL (no se copian a la imagen de Docker)
```

## Base de datos
- La contraseña va en la variable de entorno `DB_PASSWORD`. Si no existe, `comun/conexion.php` la lee del archivo `.env` de la raíz.
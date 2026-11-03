# Banco Aurora · App de cuenta y ahorro

Trabajo final de Computación Móvil. App PHP 8.2 procedural pensada para abrirse
dentro de un WebView en el celular. Tiene su propio código, su propio CSS/JS y sus
propias tablas (`bf_usuarios`, `ahorros`, `bf_historial`) y procedimientos (`sp_bf_*`)
en la misma base de datos del proyecto.

## Archivos

```
bancofinal/
  index.php          Ingreso (login) y cuenta de prueba
  registro.php       Crear cuenta
  cuenta.php         Saldo, ahorro, acciones e historial
  ahorrar.php        Pasar plata del saldo al ahorro
  movimiento.php     Consignar o retirar (?tipo=consignar|retirar)
  estado.php         Diagnóstico de la base de datos (solo lectura)
  logout.php         Cierra la sesión
  inc/conexion.php   Conexión a MySQL (Aiven) y función llamar()
  inc/funciones.php  Sesión, avisos, formato de pesos y fechas
  inc/cabecera.php   Encabezado, pantalla de carga y hojas de estilo
  inc/pie.php        Pie de página y script
  assets/css/estilos.css
  assets/js/app.js
  assets/img/logo.png, logo-512.png
  sql/bancofinal.sql
```

## 1. Crear las tablas y procedimientos

1. Abre MySQL Workbench y conéctate a la base de datos del proyecto.
2. Abre el archivo `bancofinal/sql/bancofinal.sql`.
3. Ejecuta todo el script (rayo con el nombre del archivo, o Ctrl+Shift+Enter).
4. Verifica que aparezcan `bf_usuarios`, `ahorros`, `bf_historial` y los siete
   procedimientos `sp_bf_*`.

El script se puede correr varias veces: crea las tablas solo si no existen y
reemplaza únicamente los procedimientos `sp_bf_`. No borra tablas ni toca nada
del resto del proyecto.

## 2. Contraseña de la base de datos

La conexión usa el mismo host, puerto, usuario y base que el resto del proyecto.
La contraseña se toma de la variable de entorno `DB_PASSWORD` o, si no existe, del
archivo `.env` de la raíz del repositorio:

```
DB_PASSWORD=la_clave_de_aiven
```

No hay contraseñas escritas en el código.

## 3. Correr la app

```bash
php -S 127.0.0.1:8000 -t .
```

Luego abre `http://127.0.0.1:8000/bancofinal/index.php`. También funciona con el
Dockerfile del proyecto (el document root ya es la raíz del repositorio).

Necesita la extensión `mysqli` de PHP.

## 4. Cuenta de prueba

El login trae una tarjeta con el usuario `demo` y la contraseña `1234`, y un botón
«Usar esta cuenta» que llena los campos. Esa cuenta es un usuario normal: créala
una sola vez desde `registro.php` con usuario `demo` y contraseña `1234`. Si no
existe, el botón llena los datos pero el ingreso mostrará «Usuario o contraseña
incorrectos».

## 5. Cómo funciona el dinero

- Al registrarse, cada cliente recibe `$ 500.000` de saldo y `$ 0` de ahorro.
- **Ahorrar** pasa plata del saldo al ahorro (baja el saldo, sube el ahorro).
  El formulario permite escribir el ID de otro cliente existente.
- **Consignar** suma plata al saldo.
- **Retirar** baja el saldo; el ahorro no se toca.
- Cada operación queda en `bf_historial` y se ve en «Mi cuenta» (máximo 50,
  de la más reciente a la más antigua).
- Si el saldo está en cero, la cuenta avisa:
  «No tienes saldo disponible. Consigna para poder ahorrar.»

## 6. Diagnóstico

`estado.php` solo lee: muestra si hay conexión y si las tablas y los procedimientos
responden (hace llamadas inofensivas como `sp_bf_consultar(0)`). No crea nada. Si
Aiven está apagado, ahí se ve el problema y las páginas muestran «No hay conexión
con la base de datos. Intenta de nuevo en un momento.».

## 7. WebView

- `viewport-fit=cover` y márgenes con `env(safe-area-inset-*)` para el notch.
- Todos los enlaces son relativos y no hay `target="_blank"`.
- Nada depende de `hover`: los estilos de hover están dentro de `@media (hover: hover)`.
- Después de cada POST se redirige (PRG), así el botón «atrás» no reenvía el formulario.
- La pantalla de carga dura ~1,1 s y solo sale la primera vez de la sesión del
  navegador (se guarda en `sessionStorage`), no en cada página.
- Respeta `prefers-reduced-motion`.

## 8. Pruebas manuales

1. **Caso normal:** registrarse, ver saldo `$ 500.000`, consignar `$ 100.000`,
   ahorrar `$ 50.000`, retirar `$ 30.000` y revisar el historial.
2. **ID inexistente:** en Ahorrar, ID `999` → «No existe un cliente con el ID 999».
3. **Saldo en cero:** retirar todo el saldo y entrar a Ahorrar o Retirar → aviso
   «No tienes saldo disponible…».
4. **Valor mayor al saldo:** retirar o ahorrar más de lo disponible → «Saldo
   insuficiente…» con el saldo actual.
5. **Valor cero o negativo:** escribir `0` o `-5` → «Escribe un valor entero
   mayor que cero, solo con números.».
6. **Texto en vez de número:** escribir `abc` → mismo mensaje anterior.
7. **Cerrar sesión y volver atrás:** pulsar «Salir», usar el botón atrás del
   celular → la app vuelve al login y no muestra datos de la cuenta.
8. **Sin conexión:** apagar Aiven (o dejar mal `DB_PASSWORD`) y abrir `estado.php`
   → mensaje de conexión, sin errores técnicos.

# Aurora · App de cuenta y ahorro

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
  ahorro.php         Agregar al ahorro o sacar del ahorro (siempre mi cuenta)
  enviar.php         Enviar dinero a otro cliente por su ID
  movimiento.php     Consignar o retirar (?tipo=consignar|retirar)
  estado.php         Diagnóstico de la base de datos (solo lectura)
  logout.php         Cierra la sesión
  inc/conexion.php   Conexión a MySQL (Aiven) y función llamar()
  inc/funciones.php  Sesión, avisos, formato de pesos y fechas
  inc/cabecera.php   Encabezado, pantalla de carga y hojas de estilo
  inc/pie.php        Pie de página y script
  assets/css/estilos.css
  assets/js/app.js
  assets/img/logo-aurora.png       Logo original del equipo
  assets/img/logo-aurora-512.png   Logo liviano para login y pantalla de carga
  assets/img/logo-simbolo.png      Solo la montaña (encabezado y favicon)
  sql/bancofinal.sql               Tablas y procedimientos
  sql/usuarios_prueba.sql          Usuarios prueba1, prueba2 y prueba3
```

## 1. Crear las tablas, procedimientos y usuarios de prueba

En MySQL Workbench, conéctate a la base de datos del proyecto y ejecuta los dos
scripts **en este orden**:

1. Abre `bancofinal/sql/bancofinal.sql` y ejecuta todo el script (rayo con el
   nombre del archivo, o Ctrl+Shift+Enter). Crea las tablas y los procedimientos.
   Si ya lo habías corrido antes, **vuelve a correrlo**: amplía el historial con
   «envio», «recibido» y «ahorro_retiro», y crea `sp_bf_enviar` y `sp_bf_sacar_ahorro` (no borra datos).
2. Abre `bancofinal/sql/usuarios_prueba.sql` y ejecútalo completo. Crea los
   usuarios `prueba1`, `prueba2` y `prueba3` llamando a `sp_bf_registrar`, así que
   cada uno queda también con su fila en `ahorros` y su saldo inicial.

Los dos scripts se pueden correr varias veces sin error: las tablas se crean solo
si no existen, los procedimientos `sp_bf_` se reemplazan y los usuarios que ya
existen devuelven id 0 sin duplicarse. Ningún script borra tablas ni toca otras
tablas del proyecto.

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

Necesita las extensiones `mysqli` y `mbstring` de PHP.

## 4. Cuenta de prueba

El login trae una tarjeta con el usuario `prueba1` y la contraseña `123456`, y un
botón «Usar esta cuenta» que llena los campos. Es un usuario normal de la base de
datos, creado por `usuarios_prueba.sql`. También puedes entrar con `prueba2` o
`prueba3` usando la misma contraseña.

## 5. Cómo funciona el dinero

- Al registrarse, cada cliente recibe `$ 500.000` de saldo y `$ 0` de ahorro.
- **Ahorro** (`ahorro.php`) tiene dos opciones. «Agregar al ahorro» pasa plata del
  saldo al ahorro (baja el saldo, sube el ahorro) y «Sacar del ahorro» hace lo
  contrario (`sp_bf_sacar_ahorro`).
  Siempre usa la cuenta de la sesión; ya no pide ID.
- **Enviar** pasa plata de tu saldo al saldo de otro cliente (por su ID, que se ve
  en «Mi cuenta» junto al saludo). No puedes enviarte a ti mismo. Es una sola
  transacción (`sp_bf_enviar`): quien envía queda con un «Envío» y quien recibe con
  un «Recibido» en el historial.
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
- La pantalla de carga dura ~1 s y solo sale la primera vez de la sesión del
  navegador (se guarda en `sessionStorage`), no en cada página.
- Respeta `prefers-reduced-motion`.

## 8. Pruebas manuales

1. **Caso normal:** entrar con `prueba1` / `123456`, ver saldo `$ 500.000`,
   consignar `$ 100.000`, ahorrar `$ 50.000`, retirar `$ 30.000` y revisar el
   historial.
2. **Enviar dinero:** con `prueba1` ir a Enviar, escribir el ID de `prueba2`
   (se ve en su cuenta) y un valor → «Enviaste…». Entrar con `prueba2` y ver el
   «Recibido» en su historial. Probar también un ID que no existe, el propio ID y
   un valor mayor al saldo.
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

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulo ?? 'Cuenta') ?></title>
<link rel="icon" href="assets/img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/estilos.css">
<script>
/* La animación del centro solo sale al ENTRAR a Banco (desde el menú, otra página o escribiendo la dirección).
   No sale al moverse dentro de Banco, ni al recargar o volver atrás. */
try{
  var ref=document.referrer, nav=(performance.getEntriesByType('navigation')[0]||{}).type;
  var desdeBanco=ref.indexOf(location.origin)===0 && /\/banco\//.test(ref.slice(location.origin.length));
  if(desdeBanco||nav==='reload'||nav==='back_forward')document.documentElement.classList.add('sin-loader');
}catch(e){}
</script>
<noscript><style>header,main,.brand{opacity:1!important}#loader,#logo{display:none}</style></noscript>
</head>
<body>
<header>
  <a id="slot" href="index.php" aria-label="Inicio"></a>
  <span class="brand">Cuenta</span>
  <div id="who">
    <?php if (isset($_SESSION['modo'])): ?><span><?= e($_SESSION['nombre']) ?></span><?php endif; ?>
    <a class="ghost" href="../index.php">Menú</a>
    <?php if (isset($_SESSION['modo'])): ?><a class="ghost" href="logout.php">Salir</a><?php endif; ?>
  </div>
</header>
